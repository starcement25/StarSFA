package com.forcepower.acedns.fragments;

import androidx.lifecycle.LiveData;
import androidx.lifecycle.MutableLiveData;
import androidx.lifecycle.ViewModel;

import com.forcepower.acedns.bean.CustomerInfo;

public class SharedViewModel extends ViewModel {
    private final MutableLiveData<CustomerInfo> customerInfo = new MutableLiveData<>();
    private final MutableLiveData<Integer> reportPageCount = new MutableLiveData<Integer>();
    private final MutableLiveData<String> additionalOrRecommended = new MutableLiveData<String>();
    private final MutableLiveData<String> selectedItem = new MutableLiveData<String>();
    public void selectItem(String item) {
        selectedItem.setValue(item);
    }
    public LiveData<String> getSelectedItem() {
        return selectedItem;
    }


    public void setRefreshFlag(String item) {
        additionalOrRecommended.setValue(item);
    }
    public LiveData<String> getRefreshFlag() {
        return additionalOrRecommended;
    }

    public void setSelectedCustomerInfo(CustomerInfo item) {
        customerInfo.setValue(item);
    }
    public LiveData<CustomerInfo> getSelectedCustomerInfo() {
        return customerInfo;
    }
}
