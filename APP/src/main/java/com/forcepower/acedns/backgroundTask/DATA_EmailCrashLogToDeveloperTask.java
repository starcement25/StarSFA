package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.pm.PackageInfo;
import android.os.AsyncTask;
import android.os.Build;

import com.forcepower.acedns.bean.AppInfo;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

import java.io.File;
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

public class DATA_EmailCrashLogToDeveloperTask extends AsyncTask<Void, Void, Integer> {
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
    AceDnsDatabase dbHelper;

    public DATA_EmailCrashLogToDeveloperTask(Context context, boolean showPd, String empCode, String source, String empName, boolean sendData) {
        this.mContext = context;
        this.showPd = showPd;
        this.empCode = empCode;
        this.source = source;
        this.empName = empName;
        this.sendData = sendData;
        PhoneStateChangeListener.ringing = false;
        dbHelper = new AceDnsDatabase(context);
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Integer doInBackground(Void... params) {
        String to = "acedns@coral.in";
        final String user = "acedns@coral.in";
        final String password = "acedns12345";
        MailcapCommandMap mc = (MailcapCommandMap) CommandMap.getDefaultCommandMap();
        mc.addMailcap("text/html;; x-java-content-handler=com.sun.mail.handlers.text_html");
        mc.addMailcap("text/xml;; x-java-content-handler=com.sun.mail.handlers.text_xml");
        mc.addMailcap("text/plain;; x-java-content-handler=com.sun.mail.handlers.text_plain");
        mc.addMailcap("multipart/*;; x-java-content-handler=com.sun.mail.handlers.multipart_mixed");
        mc.addMailcap("message/rfc822;; x-java-content-handler=com.sun.mail.handlers.message_rfc822");
        CommandMap.setDefaultCommandMap(mc);
        Properties properties = new Properties();
        properties.setProperty("mail.smtp.auth", "true");
        properties.setProperty("mail.smtp.starttls.enable", "true");
        properties.setProperty("mail.smtp.host", "smtp.gmail.com");
        properties.setProperty("mail.smtp.port", "587");
        properties.setProperty("mail.smtp.user", user);
        properties.setProperty("mail.smtp.password", password);

        Session session = Session.getDefaultInstance(properties,
                new javax.mail.Authenticator() {
                    protected PasswordAuthentication getPasswordAuthentication() {
                        return new PasswordAuthentication(user, password);
                    }
                });
        try {
            MimeMessage message = new MimeMessage(session);
            message.setFrom(new InternetAddress(user));
            message.addRecipient(Message.RecipientType.TO, new InternetAddress(to));
            message.setSubject(Constants.nickName.toUpperCase() + " Crash log of " + empName + "(" + empCode + ") " + " as on " + date + " @ " + time + " hrs.");
            Multipart multipart = new MimeMultipart();
            MimeBodyPart messageBodyPart1 = new MimeBodyPart();
            messageBodyPart1.setText(getDetails());
            multipart.addBodyPart(messageBodyPart1);
            if (sendData) {
                MimeBodyPart messageBodyPart2 = new MimeBodyPart();
                File filename = new File(Utils.getAppStoragePath(mContext) + "ACEdnsCrashLog.txt");
                FileDataSource source = new FileDataSource(filename);
                messageBodyPart2.setDataHandler(new DataHandler(source));
                messageBodyPart2.setFileName(source.getName());
                multipart.addBodyPart(messageBodyPart2);
            }
            message.setContent(multipart);
            Transport.send(message);
            return 1;
        } catch (Exception ex) {
            ex.printStackTrace();
        }
        return 0;
    }

    @Override
    protected void onPostExecute(Integer result) {
        super.onPostExecute(result);
        if (result == 1) {
            File filename = new File(Utils.getAppStoragePath(mContext) + "ACEdnsCrashLog.txt");
            if (filename.exists())
                filename.delete();
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
            sb.append("Device ID : ");
            sb.append("");
            sb.append("\n");
        } catch (Exception e) {
            e.printStackTrace();
        }
        details = sb.toString();
        return details;
    }
}
