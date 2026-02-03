package com.forcepower.acedns.bean;

/**
 * Created by Force Power Intellij Amiyo  on 28-08-2017.
 * Please follow standard Java coding conventions.
 * http://source.android.com/source/code-style.html
 */
public class TDAllocation {
    String mAllocationId = "";
    String mEmployeeCode = "";
    String mProductFilterCode = "";
    String mProductFilterName = "";
    String mTDMax = "0";
    String mTDAllocated = "";
    String TDAllocatedOld = "";
    String TDAllocationLowerLimit = "";
    String date = "";

    public String getAllocationId() {
        return mAllocationId;
    }

    public void setAllocationId(String allocationId) {
        this.mAllocationId = allocationId;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getEmployeCode() {
        return mEmployeeCode;
    }

    public void setEmployeCode(String employeCode) {
        this.mEmployeeCode = employeCode;
    }


    public String getProductFilterCode() {
        return mProductFilterCode;
    }

    public void setProductFilterCode(String productFilterCode) {
        this.mProductFilterCode = productFilterCode;
    }

    public String getProductFilterName() {
        return mProductFilterName;
    }

    public void setProductFilterName(String productFilterName) {
        this.mProductFilterName = productFilterName;
    }

    public String getTDMax() {
        return mTDMax;
    }

    public void setTDMax(String mTDMax) {
        this.mTDMax = mTDMax;
    }

    public String getTDAllocated() {
        return mTDAllocated;
    }

    public void setTDAllocated(String mTDAllocated) {
        this.mTDAllocated = mTDAllocated;
    }

    public String getTDAllocatedOld() {
        return TDAllocatedOld;
    }

    public void setTDAllocatedOld(String TDAllocatedOld) {
        this.TDAllocatedOld = TDAllocatedOld;
    }

    public String getTDAllocationLowerLimit() {
        return TDAllocationLowerLimit;
    }

    public void setTDAllocationLowerLimit(String TDAllocationLowerLimit) {
        this.TDAllocationLowerLimit = TDAllocationLowerLimit;
    }


}