

# HumanResource

Provide Human Resource
## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **Integer** | Primary Key |  [optional] [readonly]
**titleEn** | **String** | title in English | 
**titleBn** | **String** |  title in Bengali | 
**humanResourceTemplateId** | **Integer** | Human Resource template id |  [optional]
**organizationId** | **Integer** | Organization id | 
**organizationUnitId** | **Integer** | Organization unit id | 
**displayOrder** | **Integer** | Display order.default &#x3D;&gt;0 | 
**isDesignation** | **Integer** | 1 &#x3D;&gt; designation, 0 &#x3D;&gt; wings or section | 
**parentId** | **Integer** | Self parent id |  [optional]
**rankId** | **Integer** | Rank id |  [optional]
**status** | [**StatusEnum**](#StatusEnum) | 1 &#x3D;&gt; occupied, 2 &#x3D;&gt; vacancy ,0 &#x3D;&gt; inactive |  [optional]
**rowStatus** | [**RowStatusEnum**](#RowStatusEnum) | Activation status .1 &#x3D;&gt; active ,0&#x3D;&gt;inactive |  [optional]
**createBy** | **Integer** | Creator |  [optional]
**updatedBy** | **Integer** | Modifier |  [optional]
**createdAt** | **OffsetDateTime** |  |  [optional]
**updatedAt** | **OffsetDateTime** |  |  [optional]



## Enum: StatusEnum

Name | Value
---- | -----
NUMBER_0 | 0
NUMBER_1 | 1
NUMBER_2 | 2



## Enum: RowStatusEnum

Name | Value
---- | -----
NUMBER_0 | 0
NUMBER_1 | 1



