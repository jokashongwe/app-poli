import requests
import multiprocessing
import time
import mysql.connector as connector
import logging
from argparse import ArgumentParser

from typing import List, Dict, Any, Optional

def get_phones_from_group(group: str) -> List[str]:
    config = {
        'user': 'root',
        'password': '',
        'host': 'localhost',
        'database': 'bulksmsapp',
        'raise_on_warnings': True
    }
    group = group.strip()
    group_parts = [part for part in group.split(',') if part]
    cnx = connector.connect(**config)
    cursor = cnx.cursor()
    query = f"SELECT distinct membre.telephone as telephone  FROM membre, tag_membre WHERE tag_membre.membre_id = membre.id AND tag_membre.tag_id IN ({group});"
    if len(group_parts) == 1:
        query = f"SELECT distinct membre.telephone as telephone  FROM membre, tag_membre WHERE tag_membre.membre_id = membre.id AND tag_membre.tag_id = '{group_parts[0]}';"
    #print("Query: ", query)
    cursor.execute(query)
    numbers: List[str] = []

    for telephone in cursor:
        parsed_phone = f"{telephone}".replace("'", '').replace(",", '').replace("(", "").replace(")", '')
        numbers.append(parsed_phone)
        if len(numbers) == 5:
            yield numbers
            numbers = []
    yield numbers

    cursor.close()
    cnx.close()


class BulkOrange:
    def __init__(
        self,
        country_sender_number: str,
        config: Dict[str, str],
        message: str,
    ) -> None:
        self.country_sender_number = country_sender_number
        self.config = config
        self.message = message
        self.credentials = None
 
    def _get_credentials(self) -> Optional[Dict[str, str]]:
        auth_header = self.config.get("auth_header")
        auth_url = "https://api.orange.com/oauth/v3/token"
        headers = {
            "Authorization": f"Basic {auth_header}",
            "Content-Type": "application/x-www-form-urlencoded"
        }
        data = {"grant_type": "client_credentials"}
        response = requests.post(url=auth_url, headers=headers, data=data)
        if response.status_code != 200:
            logging.error(response.text)
            raise
        return response.json()

    def _send_message(self, phone: str) -> bool:
        senderAddress = f"tel:{self.country_sender_number}"
        url = f"https://api.orange.com/smsmessaging/v1/outbound/{senderAddress}/requests"
        body = {
            "outboundSMSMessageRequest": {
                "address": f"tel:{phone}",
                "outboundSMSTextMessage": {"message": f"{self.message}"},
                "senderAddress": f"tel:{self.country_sender_number}",
                 "senderName": "Kibulu"
            }
        }
        token_type = self.credentials.get("token_type")
        access_token = self.credentials.get("access_token")
        headers = {
            "Authorization": f"{token_type} {access_token}",
            "Content-Type": "application/json"
        }
        response = requests.post(url=url, json=body, headers=headers)
        if response.status_code != 201:
            logging.error(response.text)
            return False
        return True

    def send_messages(self, destinationList = None):
        print("Starting processing")
        
        tps = int(self.config.get("TPS", 5))
        numbers = destinationList[:tps]
        destinationList = destinationList[tps:]
        start = time.time()
        self.credentials = self._get_credentials()
        while numbers:
            # Request a new token every hour
            if int(time.time() - start) >= 3600:
                self.credentials = self._get_credentials()
            with multiprocessing.Pool(tps) as p:
                p.map(self._send_message, numbers)
            time.sleep(1.01)
            numbers = destinationList[:tps]
            destinationList = destinationList[tps:]
        print("End processing")


if __name__ == "__main__":
    parser = ArgumentParser()
    parser.add_argument("-a", "--auth", dest="auth_header",help="The Auth Token for the Orange API", metavar="AUTH")
    parser.add_argument("-m", "--message", dest="message",help="The message to send", metavar="MESSAGE")
    parser.add_argument("-p", "--phone", dest="phone", help="The phone number to send", metavar="PHONE", required=False)
    parser.add_argument("-g", "--group", dest="group", help="The group of phone numbers to send", metavar="GROUPE", required=False)

    args = parser.parse_args()

    if args.phone:
        bulk_instance = BulkOrange(
            country_sender_number= "+2430000",
            config={"auth_header": args.auth_header},
            message= args.message,
        )
        bulk_instance.send_messages(destinationList=[args.phone])
    
    if args.group:
        for numbers in get_phones_from_group(group=f"{args.group}"):
            bulk_instance = BulkOrange(
                country_sender_number= "+2430000",
                config={"auth_header": args.auth_header},
                message= args.message,
            )
            bulk_instance.send_messages(destinationList=numbers)

    
