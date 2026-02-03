package com.forcepower.acedns.bean;

public class NotificationDetails {
    public String notificationId = "";
    public String notificationType = "";
    public String senderId = "";
    public String message = "";
    public int flag = 0;
    public String ackId = "";

    public NotificationDetails() {
    }

    public NotificationDetails(String notificationId, String notificationType, String senderId, String message, int flag, String ackId) {
        this.notificationId = notificationId;
        this.notificationType = notificationType;
        this.senderId = senderId;
        this.message = message;
        this.flag = flag;
        this.ackId = ackId;
    }

    public String getNotificationId() {
        return notificationId;
    }

    public void setNotificationId(String notificationId) {
        this.notificationId = notificationId;
    }

    public String getNotificationType() {
        return notificationType;
    }

    public void setNotificationType(String notificationType) {
        this.notificationType = notificationType;
    }

    public String getSenderId() {
        return senderId;
    }

    public void setSenderId(String senderId) {
        this.senderId = senderId;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public int getFlag() {
        return flag;
    }

    public void setFlag(int flag) {
        this.flag = flag;
    }

    public String getAckId() {
        return ackId;
    }

    public void setAckId(String ackId) {
        this.ackId = ackId;
    }
}
