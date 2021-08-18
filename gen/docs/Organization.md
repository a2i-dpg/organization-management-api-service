

# Organization

provide organizations
## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **Integer** | Primary Key |  [optional] [readonly]
**titleEn** | **String** | title in English | 
**titleBn** | **String** |  title in Bengali | 
**organizationTypeId** | **Integer** | Organization type id | 
**locDivisionId** | **Integer** | location id of division |  [optional]
**locDistrictId** | **Integer** | location id of district |  [optional]
**locUpazilaId** | **Integer** | location id of upazila |  [optional]
**address** | **String** | organization address | 
**mobile** | **String** | Mobile number of organization | 
**email** | **String** | email address | 
**faxNo** | **String** |  fax number |  [optional]
**contactPersonName** | **String** | Contact person name | 
**contactPersonMobile** | **String** | Contact person mobile number | 
**contactPersonEmail** | **String** | Contact person email address | 
**contactPersonDesignation** | **String** | Contact person&#39;s designation | 
**logo** | **String** | Logo of the organization | 
**description** | **String** | Details about the organization |  [optional]
**domain** | **String** | Unique Website domain of the organization | 
**rowStatus** | [**RowStatusEnum**](#RowStatusEnum) | Activation status .1 &#x3D;&gt; active ,0&#x3D;&gt;inactive |  [optional]
**createBy** | **Integer** | Creator |  [optional]
**updatedBy** | **Integer** | Modifier |  [optional]
**createdAt** | **OffsetDateTime** |  |  [optional]
**updatedAt** | **OffsetDateTime** |  |  [optional]



## Enum: RowStatusEnum

Name | Value
---- | -----
NUMBER_0 | 0
NUMBER_1 | 1



