terraform {
  required_providers {
    aws = {
      source = "hashicorp/aws"
      version = "5.54.1"
    }
  }
}

provider "aws" {
  //region = "us-east-1"
  //access_key = "Some key"
  //secret_key = "Some key"
  //version = "value"
}

resource "aws_ecr_repository" "registry" {
  
  for_each = toset(var.names)
  name = each.key
  
  image_scanning_configuration {
    scan_on_push = true
  }
}