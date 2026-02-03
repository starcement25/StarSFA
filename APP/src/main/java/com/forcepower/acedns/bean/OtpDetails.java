package com.forcepower.acedns.bean;

public class OtpDetails {
    String mMobileNo = "";
    String mOTPCode = "";
    String mFlag = "";

    public String getMobileNo() {
        return mMobileNo;
    }

    public void setMobileNo(String mobileNo) {
        this.mMobileNo = mobileNo;
    }

    public String getOTPCode() {
        return mOTPCode;
    }

    public void setOTPCode(String OTPCode) {
        this.mOTPCode = OTPCode;
    }

    public String getFlag() {
        return mFlag;
    }

    public void setFlag(String flag) {
        this.mFlag = flag;
    }

}
