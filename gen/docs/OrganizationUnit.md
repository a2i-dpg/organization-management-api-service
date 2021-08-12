

# OrganizationUnit

Organization Unit type
## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **Integer** | Primary Key |  [optional] [readonly]
**titleEn** | **String** | title in English | 
**titleBn** | **String** |  title in Bengali | 
**organizationId** | **Integer** | Organization id | 
**organizationUnitTypeId** | **Integer** | OrganizationUnitType id | 
**locDivisionId** | **Integer** | location id of division | 
**locDistrictId** | **Integer** | location id of district | 
**locUpazilaId** | **Integer** | location id of upazila | 
**address** | **String** | organization address |  [optional]
**mobile** | **String** | Mobile number of organization |  [optional]
**email** | **String** | email address |  [optional]
**faxNo** | **String** |  fax number |  [optional]
**contactPersonName** | **String** | Contact person name |  [optional]
**contactPersonMobile** | **String** | Contact person mobile number |  [optional]
**contactPersonEmail** | **String** | Contact person email address |  [optional]
**contactPersonDesignation** | **String** | Contact person&#39;s designation |  [optional]
**employeeSize** | **Integer** | Number of employees | 
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



