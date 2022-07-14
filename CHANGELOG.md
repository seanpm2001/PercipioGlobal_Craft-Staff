# staff-management Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.0.0-beta.13 - 2022-14-07

### Changed
- Changed typings of Payrun to PayRunElementInterface for better type guarding

### Fixed
- Fixed SchemaVersion
- Fixed some typings

## 1.0.0-beta.12 - 2022-14-07

### Changed
- Changed Foreign Keys in the PayRun Services
- Changed the GQL queries to camelcasing

### Fixed
- Fixed typings on the Employee Element
- Fixed typings on the Employer Element
- Fixed typings on the PayRun Element
- Fixed typings on the PayRunEntry Element

## 1.0.0-beta.11 - 2022-06-06

### Fixed
- Fixed an issue where employer name and logo would return `null` if nested in an Employee query

## 1.0.0-beta.10 - 2022-06-06

### Changed
- Using the Craft `Craft\helpers\Json` function for JSON encodings

### Fixed
- Fixed the wrong return of payrun end dates

## 1.0.0-beta.9 - 2022-06-02

### Fixed
- Fixed the current pay run fetch on employer in the pay runs admin panel

## 1.0.0-beta.8 - 2022-05-30

### Changed
- Provide multiple options for fetching employers with tax years inside of the console
- Provided the api url inside of the settings instead of static

### Fixed
- Fixed the refresh pay runs button when there are no current results

## 1.0.0-beta.7 - 2022-05-25

### Fixed
- Removed the save value of the employer's name and logoUrl

## 1.0.0-beta.6 - 2022-05-25

### Fixed
- Fixed an issue where no data could be fetched during the GQL query

## 1.0.0-beta.5 - 2022-05-24

### Changed
- Updated the employee GQL query to provide the employer data

## 1.0.0-beta.4 - 2022-05-23

### Changed
- Removed encryption on employees of first name | last name | middle name | title
- Removed encryption on employers of name
