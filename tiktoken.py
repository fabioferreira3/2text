import sys
import tiktoken

def num_tokens_from_string(string: str, encoding_name: str) -> int:
    """Returns the number of tokens in a text string."""
    encoding = tiktoken.get_encoding(encoding_name)
    num_tokens = len(encoding.encode(string))
    return num_tokens

if __name__ == '__main__':
    if len(sys.argv) != 2:
        print("Usage: python script.py <string>")
        sys.exit(1)

    string = sys.argv[1]
    num_tokens = num_tokens_from_string(string, 'gpt2')
    print(num_tokens)
