# shareapp-api

This is a simple api developed with Symfony for a mobile application.

## 🚀 Get started to install

```
git clone https://github.com/mikayilsrt/api-shareapp.git
cd api-shareapp && composer install
...
```

# 🏁 Api

## Authenticate Route

### Register a new user
```
[POST]
http://127.0.0.1:8000/api/register
```
- [Params]
    - username: string | **required** | **unique**
    - mail: string | **required** | **unique**
    - password: string | **required**

### Authentification
```
[POST]
http://127.0.0.1:8000/api/login_check
```
- [Body]
    - username: string | **required**
    - password: string | **required**

⚠️ **Warning** : username is the user email

## User Route

### Update user account
```
[POST]
http://127.0.0.1:8000/api/user/update/{id}
```
- [Params]
    - name: string | **required**
    - username: string | **required**
    - email: string | **required**
    - password: string | **required**
    - biographie: string
    - portfolio_url: string
    - latitude: double
    - longitude: double
- [Header]
    - Authorization: Bearer Token
- [FORM_DATA]
    - profile_image: file

### Show a user information
```
[GET]
http://127.0.0.1:8000/api/user/{id}
```

## Route of collections

### Get a list of all collections
```
[GET]
http://127.0.0.1:8000/api/collection
```

### Get a collection
```
[GET]
http://127.0.0.1:8000/api/collection/{id}
```

### Create a new collection
```
[POST]
http://127.0.0.1:8000/api/collection/create
```
- [Params]
    - title: string | **required**
    - description: string | **required**
- [Header]
    - Authorization: Bearer Token
- [FORM_DATA]
    - collection_cover: file

### Update a collection
```
[POST]
http://127.0.0.1:8000/api/collection/update/{id}
```
- [Params]
    - title: string | **required**
    - description: string | **required**
- [Header]
    - Authorization: Bearer Token
- [FORM_DATA]
    - collection_cover: File

### Delete a collection
```
[DELETE]
http://127.0.0.1:8000/api/collection/delete/{id}
```
- [Header]
    - Authorization: Bearer Token

## Route of pictures

### Get a list of all pictures
```
[GET]
http://127.0.0.1:8000/api/photo/
```

### Create a new pictures
```
[POST]
http://127.0.0.1:8000/api/photo/create
```
- [Params]
    - title: string | **required**
    - description: string | **description**
    - image_file: File | **required**
    - collection_id: int | **required**
    - longitude: double
    - latitude: double
- [Header]
    - Authorization: Bearer Token

### Route of favorite
```
[POST]
http://127.0.0.1:8000/api/favorite/favorite
```
- [Params]
    - photo_id: int | **required**
- [Header]
    - Authorization: Bearer Token

⚠️ **Warning** : This route add picture in favorite or remove if already liked.