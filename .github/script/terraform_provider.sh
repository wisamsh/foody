#!/bin/bash

# Create the provider.tf file with the specified content
cat <<EOL > provider.tf
provider "aws" {
  region = "$AWS_REGION"
}

terraform {
  backend "s3" {
    bucket = "$BUCKET_NAME"
    key    = "$TF_STATE_KEY"
    region = "$AWS_REGION"
    dynamodb_table = "$DYNAMODB_TABLE_NAME"
  }
}
EOL

echo "provider.tf file created."
