# Technical Interview for Gemini Space Station, LLC (Gemini.com)

**Position:** Sr. Site Reliability Engineer (Crypto Core)

**Interviewer:** Enrique Valenzuela

**Hiring Manager:** Nate Paul

**Recruiter:** Alissa Hebert

**Candidate:** Jorge Pabón

**Date:** Monday, April 8, 2024

# Preface

The Gemini Crypto Core team values everyone’s ability to solve problems programmatically through code, with an understanding of how to deliver work to a defined specification. This is a short programming challenge to assess your technical ability to implement the sort of tooling you might be asked to create here at Gemini.  This task can be done by yourself on your own time, or in an interview setting working through your thinking in-house. If you would like to work through this in an interview please reach out to your recruiter. This is not used as a gate, but as an assessment of technical ability, and to shape further conversations.

# Challenge

**Writing in a language of your choice (python3 or golang are recommended, but not
required), and in an expected 2-4 hours,** implement monitoring alerts using our public
API - https://docs.gemini.com/rest-api. Do not go beyond 4 hours. If you hit the 4 hour
mark, just note down what you have left to do, and how you would accomplish it. The 4
hour limit is to protect your own personal time, which we don't want to take too much of.

Imagine that your script will be run periodically by a monitoring tool, being called
directly, generating alerts as output that will feed into an alerting mechanism (eg. Slack,
PagerDuty, etc). You do not need to implement this alert mechanism - just log the alert
(written to stdout) and its details per the spec below. Implementing the alert inputs as
CLI args is nice to have, but not required. Values can be hardcoded to meet the time
limit.

For each of the symbols (ie. currency pairs) that Gemini trades you must generate an
alert for the following condition -

  * Price Deviation - Generate an alert if the standard deviation from the hourly prices for past 24 hours is more than 1 (or any indicated value if there’s a CLI parameter defined in the tool)

Delivering a more correct, but less complete answer is preferable

## Output

The alert should be a JSON formatted log line that highlights the following fields -

  * Timestamp in ISO8601 format (2006-31-10T15:00:00-0500)
  * Log level - (ie. INFO for regular output. ERROR for upstream errors, and logical/math errors, DEBUG for extra data that is not required for user consumption)
  * Trading Pair: BTCUSD/ETHUSD/BTCETH/etc.
  * Deviation: true/false boolean indicating if there is a price deviation or not
  * Data: additional data regarding the log, i.e. if there was an upstream ERROR, what it was, or if there was a price deviation, what the details of that deviation were
    * Last Price: a float object that indicates what the last price was
    * Average Price: average price over the requested time period
    * Deviation: a float object that indicates the deviation from the mean represented in standard devations
    * Price change value: a float that object that indicates the deviation as a notional value

## Submission

Submit your code as a link to your git repo (eg. github, gitlab, etc with appropriate permissions shared to us: nate.paul@gemini.com, enrique.valenzuela@gemini.com, & alissa.hebert@gemini.com ), and a README text file in the repo containing:

  * Any necessary instructions for running your script
  * Any dependencies that need to be met
  * Optional: A dockerfile to run the script
  * What you would do next to further improve it
  * Other interesting checks you might implement to alert on market behaviour
  * Your approach to solving the task, and any issues you faced with implementation
  * The time it took you to write it

**Note: If the Git repository is not made public or you do not have a GitHub account,
please provide your code as a tar.gz file.**

## Scoring

Your submission will be assessed by:

  * Running the code, per your instructions, and verifying it executes as expected
  * Scoring the code with pylint, gofmt, shellcheck or an equivalent linter
  * Reviewed for code style
  * Reviewing your attached documentation
  * Quality of your documentation for running your code
  * 
**Thank you,** and please reach out through your Gemini recruiter if you have any questions.

# Appendix:
A sample run of the alert script might look something like:

```
$ ./apiAlerts.py -h

2019-08-05 16:10:51,143 - AlertingTool - INFO - Parsing args

usage: apiAlerts.py [-h] [-c CURRENCY] [-d DEVIATION]

Runs checks on API

optional arguments:

-h, --help show this help message and exit

-c CURRENCY, --currency CURRENCY The currency trading pair, or ALL

-d DEVIATION, --deviation DEVIATION Standard deviation threshold. eg. 1
```

```
$ ./apiAlerts.py -c btcusd -d 1 | jq .
{
  "timestamp": "2021-11-15T18:25:43.511Z",
  "level": "INFO",
  "trading_pair": "btcusd",
  "deviation": true,
  "data":
  {
    "last_price": "64381.98",
    "average": "65196.09",
    "change": "765.67",
    "sdev": "1.2"
  }
}
```

```
$ ./apiAlerts.py -c ethusd -d 1 | jq .
{
  "timestamp": "2021-11-15T18:25:43.511Z",
  "level": "INFO",
  "trading_pair": "ethusd",
  "deviation": true,
  "data": 
  {
    "last_price": "4607.69",
    "average": "4661.36",
    "change": "53.67",
    "sdev": "1.1"
  }
}
```

# Dependencies and other considerations

For this exercise, I have decided to use the PHP CLI.  I have written it, tested it, and packaged it using the latest Official PHP image from Docker Hub (php:latest).

## Why?

  * No need to compile as PHP is an intepreted language excellent for scripting
  * All needed dependencies are included in the base image provided by PHP.
    * PHP-CURL.
  * I'm very familiar with it and I originally thought, while reading the description, that it would end up being an API.
  * Easy to convert from CLI to actual web based API.
 
## What will you need to run this code?

  * Internet connection
  * Docker
  * Git
 
## What's included in the repository?

  * alertTool.php - The actual console application
  * Readme.md - This readme file
  * Dockerfile - The dockerfile to build the container image

# How to build and use the container and application

  1. Download this git repository's zip file or clone it
  2. Change directory to the location of the files
  3. Run the docker build command:
  
  ```
  docker build -t alert_tool .
  ```

  4. Run a container in interactive mode, exposing a bash shell, telling docker to delete it upon exit: 
  
  ```
  docker run -it --rm --name Gemini alert_tool bash
  ```

  5. Run commands on the script:
  
  ```
  php alertTool.php -h
  ```

# Approach to solving the task

  1. Read the whole document, several times, making notes and trying to understand the request.
  2. First instinct was to use either a Bash or PowerShell script to perform the actions. Reconsidered it and decided to go with PHP as it is easy to set up in a container and to transform later into an actual API if necessary.
  3. Researched the math that is necessary to perform this request.
  4. Researched the Gemini API to see what information was available and through which endpoints. Saw that it had a public API and found the needed queries to retrieve the data.
  5. Started writing the code, modularized into functions for easier readability.  Divided it into three sections: Helper Functions, Core Functions, and Execution.
  6. Used a temporary Docker container -running php:latest- to test the code while writing it.  Mounted the directory as a volume to it.
  7. Created a Dockerfile to build a custom image that contains the PHP-CLI application.
  8. Started documentation in Readme.

# Issues (and assumptions)

  1. **The documentation of the exercise was confusing.**
  
  Some things where not clearly explained pertaining the math used and it seemed to contradict what I found online while researching.  Out of the 4 hours for the exercise, the majority of the time was spent just trying to understand the request and researching.

  I attempted to reach out to Nate and Alissa per the e-mail instructions but, since I had to do this on a weekend, I didn't get a reply.  The given examples didn't provide me enough information to make my assumptions.  These were:
  
    1. The change is actually the Standard Deviation, per the math that I researched.
    2. The Standard Deviation requested, was actually some percentage relation between the change and a price (latest or average; not sure).

  2. **Confusing Gemini API documentation,** regarding how the previous 24-hour prices were returned.
  
  The wording made it sound like they were given based on price but in the end I assumed that it meant ordered by time.

  3. **Symbols returning no data or response at all.**
  
  When attempting to implement the 'ALL' query, I found that there are currency symbols that were not returning anything at all and it was causing issues with the script.  I was already running out of time, so, I disabled that section on purpose by commenting the code.

# How long did it take?

The reading and researching part was about 2 hours.

Writing the application, testing, and creating the docker file; an 2 additional hours.

Spent some extra time with writing the Readme and putting the code in GitHub.

# Improvement considerations

  1. With more time, I would have worked on getting the 'ALL' query working:

    1. Filtering out the currency symbols that were not returning anything.
    2. Aggregating the returned data into a single output collection.

  2. This issue is also present if somebody tries to do a single symbol query for one of those symbols.  It also needs handling but I didn't have time to consider it.

  3. The container is pretty big, given that I used the base php:latest image (537 MB).  With more time, I would have used Alpine, as a base, to try to reduce its size and eliminate any possible vulnerabilities that may be introduced by extra packages or add-ons.

# Last Words

I appreciate the opportunity and the time that you've taken to review and discuss this exercise with me.  Have a great day.