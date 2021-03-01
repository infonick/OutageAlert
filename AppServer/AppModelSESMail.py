# Outage Alert
# Application Server
# AppModelSESMail
#
# This code will send a list of message objects to the related recipients
#   
# notable reference: https://realpython.com/python-send-email/
#
# ---------------------------------------------------------------------------------------

import smtplib  # SMTP email library
import ssl      # SSL/TLS security


# Information for server connection 
sesServerAddress = 'email-smtp.ca-central-1.amazonaws.com'
sesPort = 465               # 465 is for secure connections like SSL/TLS
sesUsername = ''
sesPassword = ''

# Generic email information
emailSentFromAddress = "infonick@gmail.com"


def sendOutageEmailsToUsers(messages):
    # Create a secure SSL context
    context = ssl.create_default_context()

    try:
        # 'with' statement will cause the connection to cease when the block exits.
        with smtplib.SMTP_SSL(sesServerAddress, sesPort, context=context) as server:
            server.login(sesUsername, sesPassword)

            for i in range(len(messages)):
                try:
                    server.sendmail(emailSentFromAddress, messages[i]['recipientEmail'], messages[i]['emailMessage'])

                except (smtplib.SMTPSenderRefused,
                        smtplib.SMTPRecipientsRefused,
                        smtplib.SMTPDataError,
                        smtplib.SMTPHeloError,
                        smtplib.SMTPNotSupportedError) as e:
                    print(f"An error occured: {e}")
                    print(f"The related email address was: {messages[i]['recipientEmail']}")
                    print(f"The related message was: {messages[i]['emailMessage']}")
                    print(f"\n\n")
                    # TODO: add more specific error handling and logging



    except (smtplib.SMTPException,
            smtplib.SMTPServerDisconnected,
            smtplib.SMTPResponseException,
            smtplib.SMTPSenderRefused,
            smtplib.SMTPRecipientsRefused,
            smtplib.SMTPDataError,
            smtplib.SMTPConnectError,
            smtplib.SMTPHeloError,
            smtplib.SMTPNotSupportedError,
            smtplib.SMTPAuthenticationError) as e:

        print(e)
        # TODO: add more specific error handling and logging


