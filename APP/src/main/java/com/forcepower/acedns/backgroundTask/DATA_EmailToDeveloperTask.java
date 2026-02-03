package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.pm.PackageInfo;
import android.os.AsyncTask;
import android.os.Build;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.bean.AppInfo;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.FolderCompressionTask;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

import java.security.Security;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Properties;

import javax.activation.CommandMap;
import javax.activation.DataHandler;
import javax.activation.FileDataSource;
import javax.activation.MailcapCommandMap;
import javax.mail.Message;
import javax.mail.Multipart;
import javax.mail.PasswordAuthentication;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMessage;
import javax.mail.internet.MimeMultipart;

public class DATA_EmailToDeveloperTask extends AsyncTask<Void, Void, Integer> {
    Context mContext;
    String date = new SimpleDateFormat("dd-MM-yyyy").format(Calendar.getInstance().getTime());
    String time = new SimpleDateFormat("HH:mm:ss").format(Calendar.getInstance().getTime());
    String version = "1.0";
    String dbVersion = "0.0";
    PackageInfo pInfo = null;
    boolean showPd = false;
    String empCode = "";
    String source = "";
    String empName = "";
    boolean sendData;
    boolean installation = false;
    AceDnsDatabase dbHelper;

    public DATA_EmailToDeveloperTask(Context context, boolean showPd, String empCode, String source, String empName, boolean sendData) {
        this.mContext = context;
        this.showPd = showPd;
        this.empCode = empCode;
        this.source = source;
        this.empName = empName;
        this.sendData = sendData;
        PhoneStateChangeListener.ringing = false;
        dbHelper = new AceDnsDatabase(context);
    }

    public DATA_EmailToDeveloperTask(Context context, String empCode, String source, String empName, boolean sendData) {
        this.mContext = context;
        this.empCode = empCode;
        this.source = source;
        this.empName = empName;
        this.sendData = sendData;
        PhoneStateChangeListener.ringing = false;
        dbHelper = new AceDnsDatabase(context);
    }

    public DATA_EmailToDeveloperTask(Context context) {
        this.mContext = context;
        this.empCode = "";
        this.source = "";
        this.empName = "";
        this.sendData = false;
        ;
        PhoneStateChangeListener.ringing = false;
        installation = true;
        dbHelper = new AceDnsDatabase(context);
    }


    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (showPd) {
            Utils.showProgressDialog(mContext, "Data Backup is in progress..\nPlease wait.");
        }
    }

    @Override
    protected Integer doInBackground(Void... params) {
        String to = "centralcell@starcement.co.in";     // recipient
//        String to = "subhrajit.das@sbinfowaves.com";     // recipient
        final String user = "emovesfa@gmail.com";   // sender
        final String password = "koewplnyitgnzhrl";               // password

        MailcapCommandMap mc = (MailcapCommandMap) CommandMap.getDefaultCommandMap();
        mc.addMailcap("text/html;; x-java-content-handler=com.sun.mail.handlers.text_html");
        mc.addMailcap("text/xml;; x-java-content-handler=com.sun.mail.handlers.text_xml");
        mc.addMailcap("text/plain;; x-java-content-handler=com.sun.mail.handlers.text_plain");
        mc.addMailcap("multipart/*;; x-java-content-handler=com.sun.mail.handlers.multipart_mixed");
        mc.addMailcap("message/rfc822;; x-java-content-handler=com.sun.mail.handlers.message_rfc822");
        CommandMap.setDefaultCommandMap(mc);

        Security.setProperty("jdk.tls.client.protocols", "TLSv1.2");
        System.setProperty("https.protocols", "TLSv1.2");

        Properties properties = new Properties();
        properties.put("mail.smtp.host", "smtp.gmail.com");
        properties.put("mail.smtp.port", "587");
        properties.put("mail.smtp.auth", "true");
        properties.put("mail.smtp.starttls.enable", "true");
        properties.put("mail.smtp.starttls.required", "true");
        properties.put("mail.smtp.ssl.protocols", "TLSv1.2");
        properties.put("mail.smtp.ssl.trust", "smtp.gmail.com");

        Session session = Session.getInstance(properties, new javax.mail.Authenticator() {
            protected PasswordAuthentication getPasswordAuthentication() {
                return new PasswordAuthentication(user, password);
            }
        });
        try {
            MimeMessage message = new MimeMessage(session);
            message.setFrom(new InternetAddress(user, "Star Cement"));
            message.addRecipient(Message.RecipientType.TO, new InternetAddress(to));
            if (!installation) {
                if (source.length() == 0) {
                    Log.d("TAG", "_DOWNLOAD_ doInBackground 1: "+Constants.nickName.toUpperCase() + " DB Backup of " + empName + "(" + empCode + ") " + " as on " + date + " @ " + time + " hrs.");
                    message.setSubject(Constants.nickName.toUpperCase() + " DB Backup of " + empName + "(" + empCode + ") " + " as on " + date + " @ " + time + " hrs.");
                } else {
                    Log.d("TAG", "_DOWNLOAD_ doInBackground 2: "+Constants.nickName.toUpperCase() + " Network Error of " + empName + "(" + empCode + ") " + " as on " + date + " @ " + time + " hrs. " + source);
                    message.setSubject(Constants.nickName.toUpperCase() + " Network Error of " + empName + "(" + empCode + ") " + " as on " + date + " @ " + time + " hrs. " + source);
                }
            } else {
                Log.d("TAG", "_DOWNLOAD_ doInBackground 3: "+"APP Installation Notice");
                message.setSubject("APP Installation Notice");
            }
            Multipart multipart = new MimeMultipart();
            MimeBodyPart messageBodyPart1 = new MimeBodyPart();
            if (!installation) {
                Log.d("TAG", "_DOWNLOAD_ doInBackground 4: "+getDetails());
                messageBodyPart1.setText(getDetails());
            } else {
                Log.d("TAG", "_DOWNLOAD_ doInBackground 5: "+"ACEdns has been installed on the device with DeviceId : " + "");
                messageBodyPart1.setText("ACEdns has been installed on the device with DeviceId : " + "");
            }
            multipart.addBodyPart(messageBodyPart1);
            if (sendData) {
                Log.d("TAG", "_DOWNLOAD_ track 1");
                MimeBodyPart messageBodyPart2 = new MimeBodyPart();
                Log.d("TAG", "_DOWNLOAD_ track 2");
                FolderCompressionTask.zipFile(Utils.getAppStoragePath(mContext), Utils.getAppStoragePathParent(mContext) + Constants.nickName + "_" + empCode + ".zip");
                Log.d("TAG", "_DOWNLOAD_ track 3");
                String filename = Utils.getAppStoragePathParent(mContext) + Constants.nickName + "_" + empCode + ".zip";//change accordingly
                Log.d("TAG", "_DOWNLOAD_ track 4");
                FileDataSource source = new FileDataSource(filename);
                Log.d("TAG", "_DOWNLOAD_ track 5");
                messageBodyPart2.setDataHandler(new DataHandler(source));
                Log.d("TAG", "_DOWNLOAD_ track 6");
                messageBodyPart2.setFileName(source.getName());
                Log.d("TAG", "_DOWNLOAD_ track 7");
                multipart.addBodyPart(messageBodyPart2);
                Log.d("TAG", "_DOWNLOAD_ track 8");
            }



            try{
                message.setContent(multipart);
                Transport.send(message);
            }catch (Exception e){
                Log.d("TAG", "_DOWNLOAD_ doInBackground *** Exception class: " + e.getClass().getName());
                Log.d("TAG", "_DOWNLOAD_ doInBackground *** Exception message: " + e.getMessage());
                Log.d("TAG", "_DOWNLOAD_ doInBackground *** Exception toString: " + e.toString());
                e.printStackTrace();
                return 0;
            }

            return 1;
        } catch (Exception ex) {
            Log.d("TAG", "_DOWNLOAD_ sending mail doInBackground: "+ex.getMessage());
            ex.printStackTrace();
        }
        return 0;
    }

    @Override
    protected void onPostExecute(Integer result) {
        super.onPostExecute(result);
        if (showPd) {
            Utils.cancelProgressDialog();
            if (result == 1) {
                Toast.makeText(mContext, "Data was submitted successfully", Toast.LENGTH_LONG).show();
            } else {
                Toast.makeText(mContext, "Data was not submitted successfully.\nPlease try later", Toast.LENGTH_LONG).show();
            }
        }
    }


    public String getDetails() {
        String details = "";
        StringBuilder sb = new StringBuilder();
        try {
            pInfo = mContext.getApplicationContext().getPackageManager().getPackageInfo(mContext.getApplicationContext().getPackageName(), 0);
            version = pInfo.versionName;
            AppInfo info = dbHelper.getAppInfo();
            dbVersion = (info == null ? "0.00" : info.getDbVersion());
            sb.append("App Installation Time : ");
            sb.append(Utils.getInstallationTime(mContext));
            sb.append("\n");
            sb.append("App Version : ");
            sb.append(version);
            sb.append("\n");
            sb.append("DB Version : ");
            sb.append(dbVersion);
            sb.append("\n");
            sb.append("Device Manufacturer : ");
            sb.append(Build.MANUFACTURER);
            sb.append("\n");
            sb.append("Device Model : ");
            sb.append(Build.MODEL);
            sb.append("\n");
            sb.append("OS Version : ");
            sb.append(Build.VERSION.RELEASE);
            sb.append("\n");
            if (!installation) {
                sb.append("Device ID : " + Utils.getDeviceId(mContext, empCode));
            }

            sb.append("");
            sb.append("\n");
        } catch (Exception e) {
            e.printStackTrace();
        }
        details = sb.toString();
        return details;
    }
}