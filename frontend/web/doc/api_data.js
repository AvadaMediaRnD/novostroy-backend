define({ "api": [
  {
    "type": "post",
    "url": "/auth/login",
    "title": "Login",
    "version": "1.0.0",
    "name": "Login",
    "group": "Auth",
    "description": "<p>Логин пользователя, в header необходимо передать через basic авторизацию логин:пароль в base64</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Логин и пароль в base64.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Basic YWRtaW5AYWRtaW4uY29tOjExMTExMQ==\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "access_token",
            "description": "<p>Токен пользователя.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "web_url",
            "description": "<p>Ссылка на кабинет пользователя.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"code\": 100200,\n    \"status\": \"success\",\n    \"message\": \"\",\n    \"data\": {\n        \"access_token\": \"TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL\",\n        \"web_url\": \"http://{domain}/site/login?token=TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "api/modules/v1/controllers/AuthController.php",
    "groupTitle": "Auth"
  },
  {
    "type": "get",
    "url": "/cashbox/get",
    "title": "Get",
    "version": "1.0.0",
    "name": "Get",
    "group": "Cashbox",
    "description": "<p>Получение информации о состоянии касс.</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>токен пользователя.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Bearer TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "cashbox",
            "description": "<p>Данные по кассам</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "cashbox.usd",
            "description": "<p>Касса USD</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "cashbox.eur",
            "description": "<p>Касса EUR</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "cashbox.uah",
            "description": "<p>Касса UAH</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "cashbox.total",
            "description": "<p>Общая сумма в кассах, в пересчете в USD</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"code\": 100200,\n    \"status\": \"success\",\n    \"message\": \"\",\n    \"data\": {\n        \"cashbox\": {\n            \"usd\": 50000.5,\n            \"eur\": 30000,\n            \"uah\": 120000,\n            \"total\": 100000.5,\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "api/modules/v1/controllers/CashboxController.php",
    "groupTitle": "Cashbox"
  },
  {
    "type": "get",
    "url": "/cashbox/get-in-out?from=:from&to=:to",
    "title": "Get In Out",
    "version": "1.0.0",
    "name": "Get_In_Out",
    "group": "Cashbox",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "integer",
            "optional": false,
            "field": "from",
            "description": "<p>Дата период с, в формате yyyy-MM-dd (2018-01-30).</p>"
          }
        ]
      }
    },
    "description": "<p>Получить данные по приходам/расходам за указанный период. Если диапазон не указан, то возвращается за &quot;сегодня&quot;. Если нужно выбрать 1 день, то from и to передаем одинаковую дату.</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>токен пользователя.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Bearer TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "income",
            "description": "<p>Данные по приходам</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "income.usd",
            "description": "<p>Данные по приходам USD</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "income.eur",
            "description": "<p>Данные по приходам EUR</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "income.uah",
            "description": "<p>Данные по приходам UAH</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "income.total",
            "description": "<p>Суммарные данные по приходам, USD</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "outcome",
            "description": "<p>Данные по расходам</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "outcome.usd",
            "description": "<p>Данные по расходам USD</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "outcome.eur",
            "description": "<p>Данные по расходам EUR</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "outcome.uah",
            "description": "<p>Данные по расходам UAH</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "outcome.total",
            "description": "<p>Суммарные данные по расходам, USD</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"code\": 100200,\n    \"status\": \"success\",\n    \"message\": \"\",\n    \"data\": {\n        \"income\": {\n            \"usd\": 50000,\n            \"eur\": 30000,\n            \"uah\": 1200000,\n            \"total\": 100000,\n        }\n        \"outcome\": {\n            \"usd\": 50000,\n            \"eur\": 30000,\n            \"uah\": 1200000,\n            \"total\": 100000,\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "api/modules/v1/controllers/CashboxController.php",
    "groupTitle": "Cashbox"
  },
  {
    "type": "get",
    "url": "/house/get-list",
    "title": "Get List",
    "version": "1.0.0",
    "name": "Get_List",
    "group": "House",
    "description": "<p>Получить список доступных объектов, включая статистику по объектам + общую статистику.</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "Authorization",
            "description": "<p>токен пользователя.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n    \"Authorization\": \"Bearer TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Object[]",
            "optional": false,
            "field": "houses",
            "description": "<p>Список объектов с данными</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "houses.id",
            "description": "<p>ID объекта</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "houses.name",
            "description": "<p>Название объекта</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "houses.flatTotal",
            "description": "<p>Статистика по количеству квартир</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "houses.flatTotal.total",
            "description": "<p>Всего</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "houses.flatTotal.sold",
            "description": "<p>Продано</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "houses.flatTotal.available",
            "description": "<p>Доступно</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "houses.squareTotal",
            "description": "<p>Статистика по площади</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "houses.squareTotal.total",
            "description": "<p>Всего</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "houses.squareTotal.sold",
            "description": "<p>Продано</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "houses.squareTotal.available",
            "description": "<p>Доступно</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "houses.priceTotal",
            "description": "<p>Статистика по стоимости квартир</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "houses.priceTotal.total",
            "description": "<p>Всего</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "houses.priceTotal.sold",
            "description": "<p>Продано</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "houses.priceTotal.available",
            "description": "<p>Доступно</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "all",
            "description": "<p>Данные по всем объектам</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "all.flatTotal",
            "description": "<p>Статистика по количеству квартир по всем объектам</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "all.flatTotal.total",
            "description": "<p>Всего</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "all.flatTotal.sold",
            "description": "<p>Продано</p>"
          },
          {
            "group": "Success 200",
            "type": "integer",
            "optional": false,
            "field": "all.flatTotal.available",
            "description": "<p>Доступно</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "all.squareTotal",
            "description": "<p>Статистика по площади по всем объектам</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "all.squareTotal.total",
            "description": "<p>Всего</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "all.squareTotal.sold",
            "description": "<p>Продано</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "all.squareTotal.available",
            "description": "<p>Доступно</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "all.priceTotal",
            "description": "<p>Статистика по стоимости квартир по всем объектам</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "all.priceTotal.total",
            "description": "<p>Всего</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "all.priceTotal.sold",
            "description": "<p>Продано</p>"
          },
          {
            "group": "Success 200",
            "type": "float",
            "optional": false,
            "field": "all.priceTotal.available",
            "description": "<p>Доступно</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"code\": 100200,\n    \"status\": \"success\",\n    \"message\": \"\",\n    \"data\": {\n        \"houses\": [\n            {\n                \"id\": 129,\n                \"name\": \"ЖК Радостный секция 1\",\n                \"flatTotal\": {\n                    \"total\": 200,\n                    \"sold\": 50,\n                    \"available\": 150,\n                },\n                \"squareTotal\": {\n                    \"total\": 4000,\n                    \"sold\": 1000,\n                    \"available\": 3000,\n                },\n                \"priceTotal\": {\n                    \"total\": 4000000,\n                    \"sold\": 1000000,\n                    \"available\": 3000000,\n                },\n            },\n            {\n                \"id\": 130,\n                \"name\": \"ЖК Радостный секция 2\",\n                \"flatTotal\": {\n                    \"total\": 200,\n                    \"sold\": 50,\n                    \"available\": 150,\n                },\n                \"squareTotal\": {\n                    \"total\": 4000,\n                    \"sold\": 1000,\n                    \"available\": 3000,\n                },\n                \"priceTotal\": {\n                    \"total\": 4000000,\n                    \"sold\": 1000000,\n                    \"available\": 3000000,\n                },\n            },\n            {\n                \"id\": 131,\n                \"name\": \"ЖК Отдельный\",\n                \"flatTotal\": {\n                    \"total\": 200,\n                    \"sold\": 50,\n                    \"available\": 150,\n                },\n                \"squareTotal\": {\n                    \"total\": 4000,\n                    \"sold\": 1000,\n                    \"available\": 3000,\n                },\n                \"priceTotal\": {\n                    \"total\": 4000000,\n                    \"sold\": 1000000,\n                    \"available\": 3000000,\n                },\n            }\n        ],\n        \"all\": {\n            \"flatTotal\": {\n                \"total\": 200,\n                \"sold\": 50,\n                \"available\": 150,\n            },\n            \"squareTotal\": {\n                \"total\": 4000,\n                \"sold\": 1000,\n                \"available\": 3000,\n            },\n            \"priceTotal\": {\n                \"total\": 4000000,\n                \"sold\": 1000000,\n                \"available\": 3000000,\n            },\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "api/modules/v1/controllers/HouseController.php",
    "groupTitle": "House"
  }
] });
