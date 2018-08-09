#<?
[database]
;
dbtype = mysql
dbhost = localhost
dbname = voicelogger
dbport = 3306
username = root
password = 

tb_curchan = curcdr
tb_cdr = mycdr

[asterisk]

server = 127.0.0.1
port = 5038
username = astercc
secret = astercc

[licence]

licenceto = free demo
key = 
channel = 5

[103]
type = friend
host = dynamic
insecure = very
canreinvite = no
nat = yes
disallow = all
allow = ulaw,alaw,g729,g723.1
context = default
dtmfmode=rfc2833

[system]

log_enabled = 0

;Log file path
log_file_path = /tmp/astercrmDebug.log

;
; Asterisk context parameter, use which context when dial in or dial out
;

;if set to 'admin', the index page will link to "manager login" page,
;else if set to 'user' defaulf page is user login page
useindex = admin

;context when dial out, in trixbox this could be from-internal
outcontext =  default

; individual: set the limit in credit limit field to the call
; balance: set limit in balance to the call
creditlimittype = balance

upload_file_path = /var/spool/asterisk/monitor/movedvoicefiles

; astercc will refresh the balance of the group
; set to 0 if you dont want it refresh automaticly
refreshBalance = 0

; if we use history cdr(move the billed cdr to historycdr and read the cdr from historycdr)
useHistoryCdr = 1

; when we set useHistoryCDR = 1, then here set if we move the no answer cdr to historycdr
keepNoAnswerCDR = 1

; if we set clid credit
setclid = 1

;set length of clid pin number, max 20; min 10.
pin_len = 10;

; not .conf need
; if you dont want astercc generate the conf for u, just leave this value blank
sipfile = /etc/asterisk/sip_astercc

; if require valid code when login
validcode = no

[epayment]
;if enable online payment by paypaly (enable,disable)
epayment_status = disable

;Define here the URL of paypal payment (to test with sandbox)
paypal_payment_url = "https://secure.paypal.com/cgi-bin/webscr"
;paypal_payment_url = "https://www.sandbox.paypal.com/cgi-bin/webscr"

;paypal PDT verification url (to test with sandbox)
paypal_verify_url = "ssl://www.paypal.com"
;paypal_verify_url = www.sandbox.paypal.com

;paypal PDT identity token
pdt_identity_token = 

;email address for your paypal account
paypal_account = 

;name of payment item
item_name = Credit Purchase

;PayPal-Supported Currencies and Currency Codes, default USD(U.S. Dollar)
;('AUD'=>'Australian Dollar','CAD'=>'Canadian Dollar','CZK'=>'Czech Koruna','DKK'=>'Danish Krone','EUR'=>'Euro','HKD'=>'Hong Kong Dollar','HUF'=>'Hungarian Forint','ILS'=>'Israeli New Sheqel','JPY'=>'Japanese Yen','MXN'=>'Mexican Peso','NOK'=>'Norwegian Krone','NZD'=>'New Zealand Dollar','PLN'=>'Polish Zloty','GBP'=>'Pound Sterling','SGD'=>'Singapore Dollar','SEK'=>'Swedish Krona','CHF'=>'Swiss Franc','USD'=>'U.S. Dollar')
currency_code = USD

;Available amount for payer
amount = 10,20,50,100

;for IPN notify return, request internet url of asterbilling, like http://yourdomain/callshop
asterbilling_url =

;your email address for receice a notice when someone payment
notify_mail = 

[customers]
dbtype = mysql
dbhost = localhost
dbname = astercc
dbport = 3306
customertable = callshop_customers
discounttable = discount
username = astercc
password = asterccsecret

#?>
