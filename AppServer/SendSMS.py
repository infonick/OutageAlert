# url = https://canada411.yellowpages.ca/fs/1-(250-793-4004)?what=(250-793-4004)
# replace brackets () with phone number to be searched
# search for ul class="phone_details", get first item that is strong (this is the provider)

from bs4 import BeautifulSoup
import requests
import smtplib
import ssl

# this function does not really work due to people changing providers
# better to be direct and ask
def get_provider(phn):
    area_code = phn[0] + phn[1] + phn[2]
    prefix = phn[3] + phn[4] + phn[5]
    suffix = phn[6] + phn[7] + phn[8] + phn[9]

    url = "https://canada411.yellowpages.ca/fs/1-{}-{}-{}?what={}-{}-{}".\
        format(area_code, prefix, suffix, area_code, prefix, suffix)

    print(url)

    html_content = requests.get(url).text

    soup = BeautifulSoup(html_content, "lxml")

    info_div = soup.find("ul", attrs={"class": "phone__details"})
    provider = info_div.find("strong")
    return provider.text


def send_sms(phn):
    provider = get_provider(phn)
    sender_email = "aoutage@gmail.com"
    message = "trollollollollollollollollollollollollollollollollollollollollollollollollollollollollollollollo"
    port = 465
    context = ssl.create_default_context()
    with smtplib.SMTP_SSL("smtp.gmail.com", port, context=context) as server:
        server.login(sender_email, password="$*48qpALzm")

        # TODO - add the rest of the providers

        if provider == "TELUS Mobility":
            for x in range(3):
                receiver_email = "{}@msg.telus.com".format(phn)
                server.sendmail(sender_email, receiver_email, message)
        elif provider == "Rogers Communications Partnership (Wireless)":
            receiver_email = "{}@pcs.rogers.com".format(phn)
            server.sendmail(sender_email, receiver_email, message)
        elif provider == "Bell Mobility":
            for x in range(3):
                receiver_email = "{}@txt.bellmobility.ca".format(phn)
                server.sendmail(sender_email, receiver_email, message)

send_sms("2507934004")

