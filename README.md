# Roster POC

## Mockups

User context is removed for demonstration purposes.</br>
User authentication is not implemented in this POC.</br>
Roster activities are not scoped by pilot (uploading user)

## Running

docker compose build</br>
docker compose up</br>

Files of vendors and database are provided with the project.


## Test coverage

Test covereage report is available in the reoirts directory of the project. Open `index.html` in a browser to view the report.

## API

Api is available under /api web route.
To work properly, custom Accept header is requred:

**Accept: application/vnd.roster.v1+json**

Postman collection is available under /docs directory of the project.
Please define environment variable `base_url` to `http://localhost:8000` to use it.


## Activities

Dump of roster activities is available under /roster web route, just for demonstration purposes.
It is not test covered and badly affects cover report.
