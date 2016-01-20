# L²P API Client

This is a PHP implementation for the L²P API.

## Token Storage

To use this API, you have to implement a token storage which implements the ```L2PClient\Storage\StorageInterface```. A Storage class using Zend Session is already provided in ```L2PClient\Storage\ZendSessionStorage```.

## Configuration

To run the Client, you have to create a ```L2PClient\Config``` object. It recieves an instance of StorageInterface and the ClientID which is provided to you by the IT Center.

## Usage

Create an instance of ```L2PClient\Client``` and provide it with your configuration to use the API.

```
$storage = new L2PClient\Storage\ZendSessionStorage();
$config = new L2PClient\Config($storage, 'CLIENT_ID');
$client = new L2PClient\Client($config);
```

### Obtain token

To obtain an access token, you have to call the ```getAccessToken``` method. On the first call it will return null, since you have to get a RefreshToken first. There will be a DeviceToken stored in in the storage, that you can use to show the verification url to the user:

```
$token = $client->getAccessToken();
if($token === null){
	$deviceToken = $config->getStorage()->getDeviceToken();
	$verificationUrl = $deviceToken->buildVerificationUrl();
	sprintf('<a href="%s" target="_blank">Verify here</a>', $verificationUrl);
}
```

After the verification is done, the next call to ```getAccessToken``` will return an ```L2PClient\Token\AccessToken```. 

### Calling the API

After you successfully recieved an AccessToken, you can use the ```request``` function to access the API:

```
var_dump($client->request('viewAllCourseInfo'));
```
