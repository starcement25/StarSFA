import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class LogService {
  static Future<bool> logSetup(String description) async {
    try {
      final user = await UserLoginClass.getLocalUser();

      final String url =
          '${AppWebService.devBaseURL}misreport/store_emp_description.php';

      final response = await http.post(
        Uri.parse(url),
        body: {
          "emp_id": user?.empCode ?? "",
          "description": description,
        },
      );

      print(response.body);

      if (response.statusCode == 200) {
        return true;
      }

      return false;
    } catch (e) {
      print("Log API Error: $e");
      return false;
    }
  }
}
