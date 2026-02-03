package com.forcepower.acedns.bean;

public class SampleDetails {
    String referenceNo = "";
    String samplePhoto = "";
    String mAcedns = "";

    public synchronized String getreferenceNo() {
        return referenceNo;
    }

    public synchronized void setReferenceNo(String referenceNo) {
        this.referenceNo = referenceNo;
    }

    public synchronized String getSamplePhoto() {
        return samplePhoto;
    }

    public synchronized void setSamplePhoto(String samplePhoto) {
        this.samplePhoto = samplePhoto;
    }

    public synchronized String getAcedns() {
        return mAcedns;
    }

    public synchronized void setAcedns(String acedns) {
        this.mAcedns = acedns;
    }


}
