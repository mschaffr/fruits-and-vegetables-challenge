# Devlog

## What is the Devlog about?
This file contains thoughts about the development process so you can easily track the decisions made during the development of the application.

## Thoughts before starting the task

The hardest challenge for me was to find a reason to separate Fruits and Vegetables into two separate collections, as the task does not define why they have to be separated.
I tried to find a business reason for the separation of the collection, to give this theory task a purpose to fulfill this requirement.
The business reason I created in this context is that the application is for a health app that wants to provide information about foods, drinks and other consumables.
With this in mind, I created the design decisions for the API implementation.

## Upgrade to Symfony 7.3 and PHP 8.4

Reason for upgrade and choice of versions:
- PHP 8.4 is the latest stable version, offering performance enhancements, new language features and latest security fixes.
- Symfony 7.3 is the latest stable version, looking forward for easier upgrade to new long-term support (LTS) version in November 2025.
- Small task with no major changes in the codebase, so it is a good opportunity to upgrade.
- A check of all installed package dependencies will be skipped, as the task is timeboxed and would consume too much time.
- A fresh installation of Symfony could be used as well, but I want you to be able to see the changes in the codebase and the process.

## Design decisions for the goals defined in the task

Assumption: The application will be a microservice with a very limited scope and small footprint.
Based on this assumption, the application will be designed to be as lightweight as possible, with minimal dependencies and a focus on performance.
Because storage engine is not defined and irrelevant, the content will be stored in a simple JSON file.
As there's no API type defined and the data shows a simple structure of records, REST will be used and can be extended easily for all matters of data IO.

## Create a little makefile

A makefile will be created to simplify the development process and provide a consistent way to run common tasks.

## Create data from command

As it's not specified how the collections should be loaded, a command will create the data for the application.
The data will be stored at the end of the import into two JSON files in `var/data`.
The data will be loaded later in the controller to provide the REST API endpoints.

## Create REST API doc

After receiving feedback, the API will only provide a single endpoint to retrieve data.
So the API will be designed to provide a single endpoint to retrieve all data from the collections.
Considering the API structure, the import of data will convert countable IDs to UUIDs to simplify the data structure and make it more flexible for future changes and extensions.
If the API need to have ID queries, as well as the ability to remove data, it can be extended.

## Controller
The controller will be designed to be as lightweight as possible, with minimal dependencies.
The validation will be done with Symfony's built-in validation component, which will be used to validate and map the data to an object format.

## Consideration thoughts for separating Service and Import
The separation of service and import is a choice to keep the codebase clean and maintainable.
The import produces a lot of information for and after validation, which is not needed in the usual business logic.

## Tests
The tests should cover the relevant cases that had been written in the Readme, but I spend very little time on them.
