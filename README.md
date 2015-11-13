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
							'api_key'=>YOUR_API_KEY_HERE,		// API Keys available for free at SuperCron.me
							'api_secret'=>YOUR_API_SECRET_HERE,
							'external_id'=>'my unique id value',// an unique ID of your choosing.  $_SERVER['SCRIPT_NAME'] is a good way to get going quickly
							'max_run_seconds'=>60,				// max runtime
							'force_abort'=>true, 				// whether or not the object should call "exit" if the thread manager says it shouldn't run
							'grace_padding'=>5, 				// number of seconds to between graceful abort and hard abort (in this case the graceful runtime would be 55 seconds)
							));
if($threadObj->start()) {
	// grab some database records, check email servers, whatever needs to be done to see if there's a batch of work to be done
	
	while($iterator_of_some_kind && $threadObj->keep_running()) {
		// process the small pieces of code here
		// if it takes longer than the runtime allows, $threadObj->keep_running() will exit the while loop
	}
	$threadObj->stop();
}
```
