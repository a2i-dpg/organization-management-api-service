

# OrganizationType

Organization types
## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **Integer** | Primary Key |  [optional] [readonly]
**titleEn** | **String** | title in English | 
**titleBn** | **String** | title in Bengali | 
**isGovernment** | [**IsGovernmentEnum**](#IsGovernmentEnum) | Organization status . 0 &#x3D;&gt; non govt, 1 &#x3D;&gt; govt | 
**rowStatus** | [**RowStatusEnum**](#RowStatusEnum) | Activation status .1 &#x3D;&gt; active ,0&#x3D;&gt;inactive |  [optional]
**createdBy** | **Integer** | Creator |  [optional]
**updatedBy** | **Integer** | Modifier |  [optional]
**createdAt** | **OffsetDateTime** |  |  [optional]
**updatedAt** | **OffsetDateTime** |  |  [optional]



## Enum: IsGovernmentEnum

Name | Value
---- | -----
NUMBER_0 | 0
NUMBER_1 | 1



## Enum: RowStatusEnum

Name | Value
---- | -----
NUMBER_0 | 0
NUMBER_1 | 1



