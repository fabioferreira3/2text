import requests
from bs4 import BeautifulSoup, Tag
import argparse

# parser to handle command line arguments
parser = argparse.ArgumentParser(description="Web scraping script")
parser.add_argument("url", help="URL of the webpage to scrape")
parser.add_argument(
    "--html", help="Include HTML tags in output", action="store_true")
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

# remove all attributes from all tags
for tag in soup.recursiveChildGenerator():
    if tag.name:
        tag.attrs = {}

# find the 'title' tag
title_tag = soup.find('title')
title_text = "Title: "
if title_tag is not None:
    title_text += title_tag.text if not args.html else title_tag.prettify()

main_tag = soup.find('body')
content_text = ""
if main_tag is not None:
    # remove all 'footer', 'nav', 'noscript', 'img', and 'figure' tags
    for unwanted_tag in main_tag.find_all(['footer', 'nav', 'noscript', 'img', 'figure']):
        unwanted_tag.extract()

    # remove all tags that don't contain any content
    for tag in main_tag.find_all():
        if not tag.text.strip():
            tag.extract()

    # find all remaining content tags
    tags = main_tag.find_all(['h1', 'h2', 'h3', 'h4', 'h5', 'p', 'ul'])

    for tag in tags:
        content_text += "\n\n" + \
            (tag.text if not args.html else tag.prettify())

full_text = title_text + "\n" + content_text

# replace newline characters
full_text = full_text.replace('\n', '')

print(full_text)
