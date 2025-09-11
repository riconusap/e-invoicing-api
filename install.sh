#!/bin/bash

read -p "Do you agree to install this application? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]

then
    print_red "Installation aborted by user."
    exit 1
fi

print_green() {
  echo -e "\e[32m$1\e[0m"
}

# Function to print messages in red
print_red() {
  echo -e "\e[31m$1\e[0m"
}

# Function to print messages in yellow
print_yellow() {
  echo -e "\e[33m$1\e[0m"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null
then
    print_red "Docker is not installed. Please install Docker first."
    print_red "Refer to https://docs.docker.com/get-docker/ for installation instructions."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null
then
    print_red "Docker Compose is not installed. Please install Docker Compose first."
    print_red "Refer to https://docs.docker.com/compose/install/ for installation instructions."
    exit 1
fi

print_green "Starting installation process..."

# Step 1: Build and start Docker containers
print_yellow "Building and starting Docker containers..."
docker-compose up -d --build

if [ $? -ne 0 ]; then
    print_red "Failed to build and start Docker containers. Exiting."
    exit 1
fi
print_green "Docker containers are up and running."

# Step 2: Install Composer dependencies
print_yellow "Installing Composer dependencies..."
docker-compose exec app composer install

if [ $? -ne 0 ]; then
    print_red "Failed to install Composer dependencies. Exiting."
    exit 1
fi
print_green "Composer dependencies installed."

# Step 3: Copy .env.example to .env if it doesn't exist
if [ ! -f ".env" ]; then
    print_yellow "Copying .env.example to .env..."
    cp .env.example .env
    print_green ".env file created."
else
    print_yellow ".env file already exists. Skipping creation."
fi

# Step 4: Generate application key
print_yellow "Generating application key..."

docker-compose exec app php artisan key:generate

if [ $? -ne 0 ]; then
    print_red "Failed to generate application key. Exiting."
    exit 1
fi
print_green "Application key generated."

# Step 5: Run database migrations and seeders
print_yellow "Running database migrations and seeders..."
./run_migrations.sh

if [ $? -ne 0 ]; then
    print_red "Failed to run database migrations and seeders. Exiting."
    exit 1
fi
print_green "Database migrations and seeders completed."

print_green "Installation complete! Your Laravel application should now be running."
print_green "You can access it at http://localhost:8000 (or your configured port)."


