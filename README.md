# supercron-thread
Keep SuperCron-launched items from racing

This repo is designed to be used with services from SuperCron.me.  This code will prevent your code from being executed multiple times on top of itself (race conditions).  It does this by limiting the run time of the script to whatever you specify, plus it prevents other copies of the script from running by checking an external resource (SuperCron.me) to see if the script is already running.  This allows you the flexability of creating a script that will be run once per minute but can process large jobs if necessary (jobs that run longer than a minute).  To do so, change the "max_run_seconds" to however long you want the maximum time to be.  The script can then be executed once per minute and this code will prevent multiple copies from running in the event there was a large queue of work to be performed.

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
