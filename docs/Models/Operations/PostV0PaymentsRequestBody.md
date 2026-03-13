# PostV0PaymentsRequestBody


## Fields

| Field                                                        | Type                                                         | Required                                                     | Description                                                  |
| ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ |
| `contactId`                                                  | *string*                                                     | :heavy_check_mark:                                           | N/A                                                          |
| `direction`                                                  | [Operations\Direction](../../Models/Operations/Direction.md) | :heavy_check_mark:                                           | N/A                                                          |
| `externalPaymentId`                                          | *?string*                                                    | :heavy_minus_sign:                                           | N/A                                                          |
| `originatorId`                                               | *string*                                                     | :heavy_check_mark:                                           | N/A                                                          |
| `paymentAmount`                                              | *int*                                                        | :heavy_check_mark:                                           | In minor units (cents)                                       |
| `paymentNote`                                                | *?string*                                                    | :heavy_minus_sign:                                           | N/A                                                          |