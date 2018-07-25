<?php  

require_once(dirname(__FILE__) . '/app/functions.php');
require_once(dirname(__FILE__) . '/lib/mailchimp.php');

    $directory = dirname(__FILE__); 

    $file = most_recent_file($directory);

    // print "file is {$file}\n";

    $methods = get_csv_contents($file);
    // print_r(sizeof($methods));

    
    $newrecord = return_info($methods);
    // var_dump($newrecord);
    // echo "Break/";
    // print_r($newrecord);
    // print_r(sizeof($newrecord));



    //Mailchimp logic
    $chimp = new MailChimp( MAILCHIMP API KEY );

    foreach ($newrecord as $data) {
        //commented out line below and changed post method to PUT. PUT requires an md5 hash of the lower case email.
        //$result = $chimp->post("lists/" . CHIMP_LIST_ID . "/members", [
        $result = $chimp->put("lists/" . CHIMP_LIST_ID . "/members/" . md5(strtolower($data['Email'])), [
            //added the strlower function below to force lowercase to email
            'email_address' => strtolower($data['Email']),
            'status' => 'subscribed',
            'merge_fields' => [
                'FNAME' => $data["FirstName"],
                'LNAME' => $data["LastName"],
                'MMERGE19' => '00002',
                'MMERGE25' => 'TRUE'
            ]
        ]);
        print_r($result);
        print_r(sizeof($result));
    }




?>