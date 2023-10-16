import sys
import os
from dotenv import load_dotenv
from embedchain import App

# Load the environment variables from the .env file
load_dotenv()

# Define a list of allowed data types
ALLOWED_DATA_TYPES = [
    'web_page', 'youtube_video', 'pdf_file', 'sitemap', 'docx',
    'csv', 'docs_site', 'text'
]

ABS_PATH = os.getcwd() + "/oraculum"


def index_source(origin: str, collection_name: str, data_type: str):
    try:
        custom_bot = App.from_config(
            yaml_path=ABS_PATH + "/default.yaml")
        custom_bot.db.set_collection_name(collection_name)

        if data_type not in ALLOWED_DATA_TYPES:
            raise ValueError("Invalid data_type: " + data_type)

        custom_bot.add(origin, data_type=data_type)
        return True
    except Exception as e:
        print(f"Error: {e}")
        return False


if __name__ == '__main__':
    if len(sys.argv) < 3:
        print("Usage: python index-source.py <source_type> <collection_name> <source>")
        sys.exit(1)

    source_type = sys.argv[1]
    collection = sys.argv[2]
    source = sys.argv[3]
    request = index_source(source, collection, source_type)
    print(request)
