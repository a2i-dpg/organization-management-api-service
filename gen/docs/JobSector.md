

# JobSector

Job Sectors
## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **Integer** | Primary Key |  [optional] [readonly]
**titleEn** | **String** | title in English | 
**titleBn** | **String** | title in Bengali | 
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



