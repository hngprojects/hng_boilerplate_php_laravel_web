import requests
import unittest
class TeamsAuthComparisonTestCase(unittest.TestCase):
    def setUp(self):
        # Base URLs for all teams
        self.team_urls = {
            'Team Bulldozer': 'https://deployment.api-php.boilerplate.hng.tech/api/v1',
            'Team B': 'https://deployment.api-python.boilerplate.hng.tech/api/v1',
            'Team C': 'https://deployment.api-csharp.boilerplate.hng.tech/api/v1',
            'Team Kimiko': 'https://deployment.api-golang.boilerplate.hng.tech/api/v1',
            'Team Starlight': 'https://deployment.api-nestjs.boilerplate.hng.tech/api/v1'
        }
        self.headers = {'Content-Type': 'application/json'}
        # Payloads
        self.payload_register = {
            "first_name": "Team",
            "last_name": "Bulldozer",
            "email": "hng@gmail.com",
            "password": "TeamBullDozzer@hng05"
        }
        self.payload_login = {
            "email": "hng@gmail.com",
            "password": "TeamBullDozzer@hng05"
        }
        self.payload_password_reset = {
            "email": "hng@gmail.com"
        }
    def test_0_registration_comparison(self):
        responses = {}
        for team_name, base_url in self.team_urls.items():
            register_url = f'{base_url}/auth/register'
            response = requests.post(register_url, json=self.payload_register, headers=self.headers)
            responses[team_name] = {
                'status_code': response.status_code,
                'response_json': response.json()
            }
            # Print result of the registration attempt
            if response.status_code == 201:
                print(f"{team_name} Registration Success")
            else:
                print(f"{team_name} Registration Failed")
        # Validate responses for all teams
        for team_name, response_info in responses.items():
            self.assertEqual(201, response_info['status_code'],
                             f"{team_name} Registration did not return status code 201")
            data = response_info['response_json']
            self.assertIn('data', data, f"{team_name} Response JSON does not contain 'data'")
            self.assertIn('accessToken', data['data'],
                          f"{team_name} Response JSON does not contain 'accessToken' inside 'data'")
        # Compare access tokens if available
        access_tokens = [response_info['response_json']['data']['accessToken'] for response_info in responses.values()]
        if all(token == access_tokens[0] for token in access_tokens):
            print("All teams returned the same access token.")
        else:
            print("Access tokens are different across teams.")
    def login_user(self, base_url, team_name):
        url = f'{base_url}/auth/login'
        response = requests.post(url, json=self.payload_login, headers=self.headers)
        self.assertEqual(200, response.status_code, f"Login failed at {team_name}")
        response_json = response.json()
        self.assertIn('data', response_json, f"Login response JSON does not contain 'data' for {team_name}")
        self.assertIn('access_token', response_json['data'],
                      f"Login response JSON does not contain 'access_token' inside 'data' for {team_name}")
        return response_json['data']['access_token']
    def test_1_login_comparison(self):
        for team_name, base_url in self.team_urls.items():
            with self.subTest(team_name=team_name):
                self.login_user(base_url, team_name)
    def test_2_logout_comparison(self):
        for team_name, base_url in self.team_urls.items():
            with self.subTest(team_name=team_name):
                access_token = self.login_user(base_url, team_name)
                headers_with_token = {**self.headers, 'Authorization': f'Bearer {access_token}'}
                # Perform logout test
                logout_url = f'{base_url}/auth/logout'
                logout_response = requests.post(logout_url, headers=headers_with_token)
                self.assertEqual(200, logout_response.status_code, f"Logout failed at {team_name}")
                logout_response_json = logout_response.json()
                self.assertIn('message', logout_response_json,
                              f"Logout response JSON does not contain 'message' for {team_name}")
                self.assertEqual(logout_response_json['message'], "Logout successful",
                                 f"Logout message not as expected for {team_name}")
    def test_3_password_reset_comparison(self):
        for team_name, base_url in self.team_urls.items():
            with self.subTest(team_name=team_name):
                password_reset_url = f'{base_url}/auth/password-reset-email'
                password_reset_response = requests.post(password_reset_url, json=self.payload_password_reset,
                                                        headers=self.headers)
                self.assertEqual(200, password_reset_response.status_code,
                                 f"Password reset request failed at {team_name}")
                password_reset_response_json = password_reset_response.json()
                self.assertIn('message', password_reset_response_json,
                              f"Password reset response JSON does not contain 'message' for {team_name}")
                self.assertEqual(password_reset_response_json['message'], "Password reset link sent",
                                 f"Password reset message not as expected for {team_name}")
    def test_4_login_google(self):
        for team_name, base_url in self.team_urls.items():
            with self.subTest(team_name=team_name):
                google_login_url = f'{base_url}/auth/login-google'
                response = requests.get(google_login_url, headers=self.headers, allow_redirects=False)
                self.assertEqual(302, response.status_code, f"Google login redirect failed for {team_name}")
                redirect_url = response.headers.get('Location', '')
                self.assertTrue(redirect_url.startswith('https://accounts.google.com'),
                                f"Redirect URL does not start with 'https://accounts.google.com' for {team_name}")
if __name__ == "__main__":
    unittest.main()

