import requests
from bs4 import BeautifulSoup
import argparse

# parser to handle command line arguments
parser = argparse.ArgumentParser(description="Web scraping script")
parser.add_argument("url", help="URL of the webpage to scrape")
args = parser.parse_args()

# headers to mimic a browser visit
headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0;Win64) AppleWebkit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Safari/537.36'
}

# make the HTTP GET request to the given url
response = requests.get(args.url, headers=headers)
response.raise_for_status()  # raise exception if invalid response

# parse the HTML content
soup = BeautifulSoup(response.content, 'html.parser')

# find the 'title' tag
title_tag = soup.find('title')
title_text = "Title: "
if title_tag is not None:
    title_text += title_tag.get_text()

main_tag = soup.find('body')
content_text = ""
if main_tag is not None:
    tags = main_tag.find_all(['h1', 'h2', 'h3', 'h4', 'h5', 'p', 'span', 'ul'])

    for tag in tags:
        content_text += "\n\n" + tag.get_text()

full_text = title_text + "\n" + content_text

print(full_text)
