#!/bin/bash

# Replace these values with your actual data
BLOG_URL="https://your-server.com/update_image.php"
SLUG="my-shiny-apple"
NEW_IMAGE_NAME="new-apple-image"

# Execute the curl command to update the image
curl -X POST $BLOG_URL \
  -F "slug=$SLUG" \
  -F "image_name=$NEW_IMAGE_NAME"

echo -e "\nImage update request sent!" 