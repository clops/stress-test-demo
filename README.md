# PHP Wrapper for Siege

This is a simple PHP Console wrapper for the SIEGE Unix Command. The purpose of this wrapper is to streamline stress-tests on a web-application by doing some upfront setup work (like generating test data), then executing the siege command with a set of default options/settings and then emailing the results to the application maintainer.

## Installation

1. Clone the curent repository

`git clone https://github.com/clops/stress-test-demo.git`

2. Let composer do all the setup

`composer.phar install`

## Configuration Options

There are two configuration files located in `/config/` they are

* `config/.siegerc` -- this is the default siege configuration
* `config/urls.txt` -- this is the list of URLs to stress-test

## Usage Examples

Usage is simple, and is limited to one console command

`$ ./console help siege
Usage:
  siege [options]

Options:
  -g, --generate[=GENERATE]        Full Update including all dependancies [default: 100]
  -c, --concurrent[=CONCURRENT]    Number of concurrent threads to run [default: 10]
  -r, --repetitions[=REPETITIONS]  Number of repetitions per thread per URL [default: 10]
  -h, --help                       Display this help message
  -q, --quiet                      Do not output any message
  -V, --version                    Display this application version
      --ansi                       Force ANSI output
      --no-ansi                    Disable ANSI output
  -n, --no-interaction             Do not ask any interactive question
  -v|vv|vvv, --verbose             Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Stress Test the Application`

## Achtung
This is experimental software which shall not be used in production

## External Libraries Used
* Symfony 3.1 
* SIEGE