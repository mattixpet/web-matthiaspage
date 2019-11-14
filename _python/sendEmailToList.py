# Small script to use emails from emaillist.txt and send email to them.
# Includes an unsubscribe link with a hash using a security code both here locally
# and on the server (in php) (code+email)

# Needs files subject.txt, content.txt (email body), emaillist.txt and emaillist_code.txt

import smtplib

from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

import hashlib

def main():
	subject = '' # just one line, txt
	with open('subject.txt', 'r', encoding="utf-8") as f:
		subject = f.read()

	content = '' # html
	with open('content.txt', 'r', encoding="utf-8") as f:
		content = f.read()

	# connect to email server
	server = smtplib.SMTP('smtp.gmail.com', 587)
	server.ehlo()
	server.starttls()
	server.ehlo()
	server.login("matthias.crazy.email.bot@gmail.com", "my super secret password !")

	# get list of emails from this file
	emaillist = []
	with open('emaillist.txt', 'r', encoding="utf-8") as f:
		emaillist = f.readlines()
	emaillist = [x.strip() for x in emaillist]

	# code to add to email address to create a hash to verify the one who is unsubscribing
	# is doing it from his email address (same code is used in php to verify the unsubscribe)
	code = ''
	with open('emaillist_code.txt', 'r', encoding="utf-8") as f:
		code = f.read()

	for email in emaillist:
		fromaddr = "matthias.crazy.email.bot@gmail.com"
		emailHash = hashlib.sha256((code+email).encode()).hexdigest()

		msg = MIMEMultipart()
		msg['From'] = fromaddr
		msg['To'] = email
		msg['Subject'] = subject
		msg.attach(MIMEText(content.format(email=email, emailHash=emailHash), 'html'))
		text = msg.as_string()

		# send mail
		print('Sending email to: {}'.format(email))
		server.sendmail(fromaddr, email, text)

if __name__ == "__main__":
	main()