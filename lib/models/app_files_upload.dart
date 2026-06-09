import 'dart:io';
import 'package:http/http.dart' as http;

import 'package:archive/archive_io.dart';
import 'package:path_provider/path_provider.dart';
import 'package:starsfa/log/log_service.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class AppFilesUpload {
  // Function to generate zip of content of Images folder
  static Future<String> generateImagesZip() async {
    Directory directory = await getTemporaryDirectory();
    directory = Directory('${directory.path}/Images');
    // Get the list of files in the Images folder
    List<FileSystemEntity> files = await directory.list().toList();
    final user = await UserLoginClass.getLocalUser();
    String zipPath = '${directory.path}/${user?.empCode}.zip';
    // Create a zip file
    final zipFile = File(zipPath);
    // Create a zip encoder
    final zipEncoder = ZipFileEncoder();
    // Open the zip file
    zipEncoder.create(zipFile.path);
    // Add files to the zip file
    for (FileSystemEntity file in files) {
      zipEncoder.addFile(File(file.path));
    }
    // Close the zip file
    zipEncoder.close();
    return zipFile.path;
  }

  static Future<bool> uploadImageZip() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    String zipPath = await generateImagesZip();
    await LogService.logSetup('AppFilesUpload $zipPath');
    // Upload the zip file to the server
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.attachmentExportURL}?emp_code=${user?.empCode}&nick_name=${AppWebService.nickname}');
        await LogService.logSetup('${AppWebService.attachmentExportURL}?emp_code=${user?.empCode}&nick_name=${AppWebService.nickname}');
    var request = http.MultipartRequest('POST', url);
    request.files.add(await http.MultipartFile.fromPath('file', zipPath));
    http.StreamedResponse response = await request.send();
    final body = await response.stream.bytesToString();
    await LogService.logSetup('AppFilesUpload ${body.toString()}');
    if (response.statusCode == 200 && body.contains('1')) {
      // empty the Images folder
      Directory directory = await getTemporaryDirectory();
      directory = Directory('${directory.path}/Images');
      List<FileSystemEntity> files = await directory.list().toList();
      for (FileSystemEntity file in files) {
        file.delete();
      }
      return true;
    } else {
      // delete the zip file
      File(zipPath).delete();
      return false;
    }
  }

  static Future<bool> uploadImageZip1() async {
    try {
      // Check network connection
      bool isConnected = await NetworkService.checkConnectionAll();
      if (!isConnected) {
        return false;
      }
      // Generate ZIP file from images
      String zipPath = await generateImagesZip();
      await LogService.logSetup('AppFilesUpload $zipPath');
      // Get user info
      final user = await UserLoginClass.getLocalUser();
      if (user == null) {
        return false;
      }
      // Construct URL
      Uri url = Uri.parse(
          '${AppWebService.attachmentExportURL}?emp_code=${user.empCode}&nick_name=${AppWebService.nickname}');
    await LogService.logSetup('${AppWebService.attachmentExportURL}?emp_code=${user.empCode}&nick_name=${AppWebService.nickname}');
      // Prepare HTTP request
      var request = http.MultipartRequest('POST', url);
      request.files.add(await http.MultipartFile.fromPath('file', zipPath));
      // Send request
      http.StreamedResponse response = await request.send();
      final body = await response.stream.bytesToString();
      await LogService.logSetup('AppFilesUpload ${response.statusCode}');
      // Handle successful upload
      if (response.statusCode == 200 && body.contains('1')) {
        Directory tempDir = await getTemporaryDirectory();
        Directory imageDir = Directory('${tempDir.path}/Images');
        if (await imageDir.exists()) {
          List<FileSystemEntity> files = await imageDir.list().toList();
          for (FileSystemEntity file in files) {
            await file.delete();
          }
        }
        // Optionally delete the zip file as well
        try {
          final zipFile = File(zipPath);
          if (await zipFile.exists()) {
            await zipFile.delete();
          }
        // ignore: empty_catches
        } catch (e) {}
        await LogService.logSetup('AppFilesUpload Success');
        return true;
      } else {
        // On failure, delete the zip file
        try {
          final zipFile = File(zipPath);
          if (await zipFile.exists()) {
            await zipFile.delete();
          }
        // ignore: empty_catches
        } catch (e) {}
         await LogService.logSetup('AppFilesUpload failed');
        return false;
      }
    } catch (e) {
      return false;
    }
  }
}
