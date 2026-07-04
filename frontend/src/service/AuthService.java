package service;

import config.ApiConfig;
import dto.LoginRequest;
import dto.LoginResponse;
import dto.RegisterRequest;
import utils.ApiClient;

/**
 * Sprint 1 network bridge for identity/security.
 *
 * This is the ONLY place that talks to Duncan's Laravel/Sanctum register
 * and login endpoints. Screens (Reg, LoginController) call these methods
 * and nothing else — they never build HTTP requests or JSON themselves.
 * That separation is what the SDD's "Java-Laravel Bridge Management"
 * section (Anthony = Controller, Patience = UI only) actually requires.
 */
public class AuthService {

    public static LoginResponse register(String name, String email, String password, String passwordConfirmation)
    {
        RegisterRequest request = new RegisterRequest(name, email, password, passwordConfirmation);
        ApiClient.ApiResponse response = ApiClient.post(ApiConfig.REGISTER_ENDPOINT, request.toJson(), null);
        return LoginResponse.fromJson(response.statusCode, response.body);
    }

    public static LoginResponse login(String email, String password) {
        LoginRequest request = new LoginRequest(email, password);
        ApiClient.ApiResponse response = ApiClient.post(ApiConfig.LOGIN_ENDPOINT, request.toJson(), null);
        return LoginResponse.fromJson(response.statusCode, response.body);
    }
}
