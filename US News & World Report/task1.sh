#!/bin/bash

# Check if the directory path was provided on the script call
if [ -z "$1" ]; then
  echo "Error!  You must provide a path to create."
  echo "Example: $0 <path>"
  exit 1
fi


# Check if already exists
if [ -d "$1" ]; then 
  echo "Directory '$1' already exists."
else  
  # Create the directory
  mkdir -p "$1"
  # Allow anyone to read, write, and modify files and directories
  chmod 777 "$1"
  echo "Directory '$1' was created.  Full permissions were given to everyone."
fi