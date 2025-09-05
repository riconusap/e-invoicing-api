#!/bin/bash

# PHP Unit Test Runner Script for Laravel Project
# This script provides various options for running tests

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_color() {
    printf "${1}${2}${NC}\n"
}

# Function to show usage
show_usage() {
    print_color $BLUE "PHP Unit Test Runner for Laravel"
    echo ""
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -a, --all           Run all tests"
    echo "  -u, --unit          Run only unit tests"
    echo "  -f, --feature       Run only feature tests"
    echo "  -c, --coverage      Run tests with coverage report"
    echo "  -v, --verbose       Run tests in verbose mode"
    echo "  -t, --test NAME     Run specific test file"
    echo "  -g, --group GROUP   Run tests in specific group"
    echo "  -h, --help          Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 -a                    # Run all tests"
    echo "  $0 -u -v                 # Run unit tests verbosely"
    echo "  $0 -c                    # Run all tests with coverage"
    echo "  $0 -t UserTest           # Run UserTest specifically"
    echo "  $0 -f --coverage         # Run feature tests with coverage"
}

# Default values
RUN_ALL=false
RUN_UNIT=false
RUN_FEATURE=false
COVERAGE=false
VERBOSE=false
SPECIFIC_TEST=""
GROUP=""

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -a|--all)
            RUN_ALL=true
            shift
            ;;
        -u|--unit)
            RUN_UNIT=true
            shift
            ;;
        -f|--feature)
            RUN_FEATURE=true
            shift
            ;;
        -c|--coverage)
            COVERAGE=true
            shift
            ;;
        -v|--verbose)
            VERBOSE=true
            shift
            ;;
        -t|--test)
            SPECIFIC_TEST="$2"
            shift 2
            ;;
        -g|--group)
            GROUP="$2"
            shift 2
            ;;
        -h|--help)
            show_usage
            exit 0
            ;;
        *)
            print_color $RED "Unknown option: $1"
            show_usage
            exit 1
            ;;
    esac
done

# If no specific option is provided, run all tests
if [[ $RUN_ALL == false && $RUN_UNIT == false && $RUN_FEATURE == false && -z $SPECIFIC_TEST && -z $GROUP ]]; then
    RUN_ALL=true
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    print_color $RED "Error: vendor directory not found. Please run 'composer install' first."
    exit 1
fi

# Check if PHPUnit is available
if [ ! -f "vendor/bin/phpunit" ]; then
    print_color $RED "Error: PHPUnit not found. Please run 'composer install' first."
    exit 1
fi

# Prepare the command
CMD="./vendor/bin/phpunit"

# Add verbosity
if [[ $VERBOSE == true ]]; then
    CMD="$CMD --verbose"
fi

# Add coverage
if [[ $COVERAGE == true ]]; then
    CMD="$CMD --coverage-html coverage-html --coverage-text"
    print_color $YELLOW "Coverage reports will be generated in ./coverage-html/"
fi

print_color $BLUE "Starting PHP Unit Tests..."
print_color $BLUE "=================================="

# Run specific test
if [[ ! -z $SPECIFIC_TEST ]]; then
    print_color $YELLOW "Running specific test: $SPECIFIC_TEST"
    $CMD --filter $SPECIFIC_TEST
    exit 0
fi

# Run specific group
if [[ ! -z $GROUP ]]; then
    print_color $YELLOW "Running test group: $GROUP"
    $CMD --group $GROUP
    exit 0
fi

# Run unit tests
if [[ $RUN_UNIT == true ]]; then
    print_color $YELLOW "Running Unit Tests..."
    $CMD --testsuite Unit
fi

# Run feature tests
if [[ $RUN_FEATURE == true ]]; then
    print_color $YELLOW "Running Feature Tests..."
    $CMD --testsuite Feature
fi

# Run all tests
if [[ $RUN_ALL == true ]]; then
    print_color $YELLOW "Running All Tests..."
    $CMD
fi

print_color $GREEN "Tests completed!"

# Show coverage info if generated
if [[ $COVERAGE == true ]]; then
    print_color $GREEN "Coverage report generated in ./coverage-html/"
    if [ -f "coverage.txt" ]; then
        print_color $BLUE "Coverage Summary:"
        tail -n 10 coverage.txt
    fi
fi