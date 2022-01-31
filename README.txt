Follow the below steps to run the application:


1. Login to your google account, navigate to https://console.cloud.google.com/home,  Create New Project, Enable "Google Drive" and "Google Sheet" API.
2. Navigate to "API & Services" credentials page, Click on "Manage Service Accounts" link and create new service account.
3. Post creating the service account, Create new service account key.
4. Download the service account key to "configs\google_api_service_account" folder, and rename the same to "credentials.json"
5. Place the feed that you need to read under "data\feed" folder.


From "Productsup" root folder, run the following commands to test the script:

php bin\console read-xml
php bin\console read-xml -f coffee_feed.xml
php bin\console read-xml -f http://localhost/productsup/data/feed/coffee_feed.xml


Sample Output:

Starting to read the file http://localhost/productsup/data/feed/coffee_feed.xml
File Processed Successfully.
Google Sheet URL: https://docs.google.com/spreadsheets/d/1o1CoU9nXYZDUdAz5bhfJANlS58kr8d1r60BmudTl-DE
Complete.

Testing finally