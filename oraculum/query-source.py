import sys
import os
from embedchain import App

ABS_PATH = os.getcwd() + "/oraculum"


def query_source(collection_name: str, question: str):
    try:
        custom_bot = App.from_config(
            yaml_path=ABS_PATH + "/default.yaml")
        custom_bot.db.set_collection_name(collection_name)
        response = custom_bot.chat(question)
        return response
    except Exception as e:
        print(f"Error: {e}")
        return None


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Usage: python query-source.py <collection_name> <question>")
        sys.exit(1)

    collection = sys.argv[1]
    question = sys.argv[2]
    request = query_source(collection, question)
    print(request)
