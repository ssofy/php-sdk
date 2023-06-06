# CHANGELOG

## 2.0.0 - 2023-06-06

* Renamed the `Client` to `APIClient` and `ClientConfig` to `APIConfig` for more readability. 
* The `OAuth2Config` expects only the `url` parameter be given.
The necessary paths for different actions will be appended by the SDK.
* Added the `locale` config parameter to `OAuth2Config`.
* New `OAuth2Client` methods: `initSocialAuthCodeFlow()`, `getLogoutUrl()`, `getAccountUrl()`
* Bug Fixes.

## 1.1.9 - 2023-06-01

* Added the missing `username` property in UserEntity.

## 1.1.8 - 2023-05-29

* Added the missing UserEntity properties.

## 1.1.7 - 2023-03-24

* Fixed the minimum stability issue.

## 1.1.6 - 2023-03-19

* Switched to the new User Resource endpoint.

## 1.1.5 - 2023-03-14

* Fixed issues with APIResponse and Token models.

## 1.1.4 - 2023-02-28

* Compliance with Draft 1.30 signature style.

## 1.1.3 - 2023-02-11

* Added missing UserEntity properties.

## 1.1.2 - 2023-02-04

* Added OTPOptionEntity missing properties.
* Fixed nested value normalization with BaseModel::toArray().

## 1.1.1 - 2023-01-24

* Added UserEntity missing properties.
* InvalidValueException hint removed from model constructor.

## 1.1.0 - 2023-01-19

* Moved entities to the respective directory at Models/Entities.
* Added setter/getters to ClientConfig and OAuth2Config classes.
* Added OTPOptionEntity model.
* Added the missed "hash" property to UserEntity model.

## 1.0.0 - 2022-12-16

* First Release.
