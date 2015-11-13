# supercron-thread
Keep SuperCron-launched items from racing

This repo is designed to be used with services from SuperCron.me.  This code will prevent your code from being executed multiple times on top of itself (race conditions).

What to do:

1. Create an account on SuperCron.me and grab your API Key and API Secret

How to integrate into your code:

1. create the object with the necessary parameters
2. Test to see if it's OK to start
3. While looping

```
$threadObj = new sc_thread(array(
	'api_key'=>YOUR_API_KEY_HERE,
	'api_secret'=>YOUR_API_SECRET_HERE,
	'max_run_seconds'=>60,
	'external_id'=>$_SERVER['SCRIPT_NAME'] // or any string identifier
	));


if($threadObj->start()) {
	/*
		Grab your records here.  Database queries or arrays
		work well.  Something to iterate over.
	*/
	
	while($iterator_of_some_kind && $threadObj->keep_running()) {
		/*
			Process your records here.
			If it takes longer than the runtime allows,
			$threadObj->keep_running() will exit the while loop
			gracefully.
		*/
	}
	
	/*
		Make sure to call "stop" before finishing so the system
		knows your script ended successfully!
	*/
	$threadObj->stop();
}
```
