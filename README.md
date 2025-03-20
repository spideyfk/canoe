### Prerequiresites

- composer (https://getcomposer.org/)
- docker (https://docs.docker.com/get-docker/)

After docker installed it may prompt to run the following command in you terminal. **DO NOT RUN**, since it will use the port :80 which will need to the application.
![Docker!](https://user-images.githubusercontent.com/207759/84058442-872ae280-a9b9-11ea-9853-6899721d7213.png)

### Setup Env

```
copy .env.example into .env
```

### Docker Config
###### (if needed)
```
Update your docker to use at least 4 GB memory
```


### Start the app

```
./dev up
```

Containers:

```
laravel: php
mysql_db: mysql
```

### Get into shell
###### (if needed)
```
./dev shell
```

### Endpoints and how to run
```
This endpoints can be run using postman. I'm including the full path to test with this docker env.

GET http://localhost:8080/api/funds -> This will return a list of funds

POST http://localhost:8080/api/funds
{
    "name": "ABC HEdge Fund",
    "start_year": 2025,
    "manager_id": 1,
    "aliases": [
        { "name": "testme" },
        { "name": "another alias" }
    ],
    "company_ids": [1, 2, 3]
}

PUT http://localhost:8080/api/funds/{fund_id}
{
    "name": "ABC HEdge Fund 2",
    "start_year": 2025,
    "manager_id": 2,
    "aliases": [
        { "name": "testme 2" },
        { "name": "another alias 2" }
    ],
    "company_ids": [1, 2, 3]
}

GET http://localhost:8080/api/funds/potential-duplicates

POST http://localhost:8080/api/fund-managers
{
    "name": "Michael Scott"
}

POST http://localhost:8080/api/companies
{
    "name": "Dunder Mifflin"
}

POST http://localhost:8080/api/aliases
{
    "name": "Test Alias",
    "fund_id": 1
}
```
