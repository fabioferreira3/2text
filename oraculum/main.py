import sys
from dotenv import load_dotenv
from embedchain import App
from embedchain.config import AppConfig

# Load the environment variables from the .env file
load_dotenv()

# Define a list of allowed data types
ALLOWED_DATA_TYPES = [
    'web_page', 'youtube_video', 'pdf_file', 'sitemap', 'docx',
    'csv', 'docs_site', 'text'
]


def add_data(origin: str, collection_name: str, data_type: str):
    try:
        config = AppConfig(collection_name=collection_name)
        custom_bot = App(config)
        custom_bot.db.set_collection_name(collection_name)

        if data_type not in ALLOWED_DATA_TYPES:
            raise ValueError("Invalid data_type: " + data_type)

        custom_bot.add(origin, data_type=data_type)
        print("Data added successfully")
    except Exception as e:
        print(f"Error: {e}")


def query_data(question: str, collection_name: str):
    try:
        config = AppConfig(collection_name=collection_name)
        custom_bot = App(config)
        response = custom_bot.query(question)
        print(response)
    except Exception as e:
        print(f"Error: {e}")


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: script_name command [arguments...]")
        sys.exit(1)

    command = sys.argv[1]

    if command == "add":
        if len(sys.argv) != 5:
            print("Usage: script_name add origin collection_name data_type")
            sys.exit(1)
        add_data(sys.argv[2], sys.argv[3], sys.argv[4])
    elif command == "query":
        if len(sys.argv) != 4:
            print("Usage: script_name query question collection_name")
            sys.exit(1)
        query_data(sys.argv[2], sys.argv[3])
    else:
        print(f"Unknown command: {command}")
