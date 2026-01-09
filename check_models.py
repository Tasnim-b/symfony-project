from google import genai
import os

# Using the hardcoded key that we know reaches the server (auth works)
api_key = "AIzaSyCWVJr7isu7B-UdDW9NwT3pnGIfgEgYzgo"

print("Initializing client...")
try:
    client = genai.Client(api_key=api_key)
    print("Listing models...")
    # The new SDK list iterator
    with open('found_models.txt', 'w', encoding='utf-8') as f:
        for m in client.models.list():
            if 'flash' in m.name:
                print(f"Found model: '{m.name}'")
                f.write(f"{m.name}\n")
    
    candidates = [
        "gemini-2.0-flash-exp",
        "gemini-2.0-flash-001",
        "gemini-flash-latest",
        "gemini-1.5-flash-latest", # Trying this just in case
        "gemini-1.5-pro-latest"
    ]
    
    for model in candidates:
        print(f"\nAttempting generation with '{model}'...")
        try:
            response = client.models.generate_content(
                model=model,
                contents="Hello, are you working?"
            )
            print(f"SUCCESS with {model}: {response.text}")
            break # Stop if we find one
        except Exception as e:
            print(f"FAILED with {model}: {e}")
except Exception as e:
    print(f"Error: {e}")
