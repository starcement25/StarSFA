package com.forcepower.acedns.bean;

public class CustomerDetails {
    String customerCode = "", customerName = "", routeCode = "", empCode = "", isBlackList = "", isACEDNS = "",
            creditLimit = "", currentBalance = "", tradeDiscount = "0", customerType = "", rdsTag = "NA",
            flag = "", address = "", number = "", pin = "", newRouteouteCode = "", newRouteName = "", remarks = "",
            replacingCustCode = "", mSaudaValidityPeriod = "", mCheckFlag = "", mRouteName = "", landlineNo = "",
            ownerName = "", ownerPhone = "", custClass = "", weeklyClosingDay = "", coverageType = "", TIN = "",
            PAN = "", minimumStock = "", image = "", branchCode = "", visitDay = "", email = "", firmName = "",
            propName = "", gstNo = "", adharNo = "", TaggedCustomerCode = "", IncoTerms = "", LoadabilityTon = "",
            sauda_limit = "", pending_qty = "", TransportMode = "", state = "", saudaType = "", zone = "",
            visitSequence = "", activated = "", activated_customer_code = "", retailer_app = "", base_latt = "",
            base_longi = "", need_location_update = "", customerImage = "", ciLogic = "", categoryOfStore = "",
            inStoreActivityPossible = "", isNewCustomer = "", outletImage = "", ownerImage = "", gstImage = "",
            adharImage = "", drCategory = "", whatsappNumber = "", dateOfBirth = "", dateOfAnniversary = "",
            spouseDateOfBirth = "";
    Double freightCharge = 0.0;
    int ciLogicPriority = 0;

    public String getWhatsappNumber() {
        return whatsappNumber;
    }

    public void setWhatsappNumber(String whatsappNumber) {
        this.whatsappNumber = whatsappNumber;
    }

    public String getDateOfBirth() {
        return dateOfBirth;
    }

    public void setDateOfBirth(String dateOfBirth) {
        this.dateOfBirth = dateOfBirth;
    }

    public String getDateOfAnniversary() {
        return dateOfAnniversary;
    }

    public void setDateOfAnniversary(String dateOfAnniversary) {
        this.dateOfAnniversary = dateOfAnniversary;
    }

    public String getSpouseDateOfBirth() {
        return spouseDateOfBirth;
    }

    public void setSpouseDateOfBirth(String spouseDateOfBirth) {
        this.spouseDateOfBirth = spouseDateOfBirth;
    }

    public String getLandlineNo() {
        return landlineNo;
    }

    public void setLandlineNo(String landlineNo) {
        this.landlineNo = landlineNo;
    }

    public String getOwnerName() {
        return ownerName;
    }

    public void setOwnerName(String ownerName) {
        this.ownerName = ownerName;
    }

    public String getOwnerPhone() {
        return ownerPhone;
    }

    public void setOwnerPhone(String ownerPhone) {
        this.ownerPhone = ownerPhone;
    }

    public String getCustClass() {
        return custClass;
    }

    public void setCustClass(String custClass) {
        this.custClass = custClass;
    }

    public String getWeeklyClosingDay() {
        return weeklyClosingDay;
    }

    public void setWeeklyClosingDay(String weeklyClosingDay) {
        this.weeklyClosingDay = weeklyClosingDay;
    }

    public String getCoverageType() {
        return coverageType;
    }

    public void setCoverageType(String coverageType) {
        this.coverageType = coverageType;
    }

    public String getTIN() {
        return TIN;
    }

    public void setTIN(String tIN) {
        TIN = tIN;
    }

    public String getPAN() {
        return PAN;
    }

    public void setPAN(String pAN) {
        PAN = pAN;
    }

    public String getDnsCustCode() {
        return replacingCustCode;
    }

    public void setDnsCustCode(String replacingCustCode) {
        this.replacingCustCode = replacingCustCode;
    }

    public String getFlag() {
        return flag;
    }

    public void setFlag(String flag) {
        this.flag = flag;
    }

    public String getRdsTag() {
        if (rdsTag == null) {
            rdsTag = "";
        }
        return rdsTag;
    }

    public void setRdsTag(String rdsTag) {
        this.rdsTag = rdsTag;
    }

    public void setNewRouteouteCode(String newRouteouteCode) {
        this.newRouteouteCode = newRouteouteCode;
    }

    public String getCustomerType() {
        return customerType;
    }

    public void setCustomerType(String customerType) {
        this.customerType = customerType;
    }

    public String getTradeDiscount() {
        return tradeDiscount;
    }

    public void setTradeDiscount(String tradeDiscount) {
        this.tradeDiscount = tradeDiscount;
    }

    public final String getAddress() {
        return address;
    }

    public final void setAddress(String address) {
        this.address = address;
    }

    public final String getNumber() {
        return number;
    }

    public final void setNumber(String number) {
        this.number = number;
    }

    public final String getPin() {
        return pin;
    }

    public final void setPin(String pin) {
        this.pin = pin;
    }

    public String getNewRouteCode() {
        return newRouteouteCode;
    }

    public String getNewRouteName() {
        return newRouteName;
    }

    public void setNewRouteName(String newRouteName) {
        this.newRouteName = newRouteName;
    }

    public final String getRemarks() {
        return remarks;
    }

    public final void setRemarks(String remarks) {
        this.remarks = remarks;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public String getCustomerName() {
        return customerName;
    }

    public void setCustomerName(String customerName) {
        this.customerName = customerName;
    }

    public String getRouteCode() {
        return routeCode;
    }

    public void setRouteCode(String routeCode) {
        this.routeCode = routeCode;
    }

    public String getEmpCode() {
        return empCode;
    }

    public void setEmpCode(String empCode) {
        this.empCode = empCode;
    }

    public String getCurrentBalance() {
        return currentBalance;
    }

    public void setCurrentBalance(String currentBalance) {
        this.currentBalance = currentBalance;
    }

    public String getCreditLimit() {
        return creditLimit;
    }

    public void setCreditLimit(String creditLimit) {
        this.creditLimit = creditLimit;
    }

    public String getIsACEDNS() {
        return isACEDNS;
    }

    public void setIsACEDNS(String isACEDNS) {
        this.isACEDNS = isACEDNS;
    }

    public String getIsBlackList() {
        return isBlackList;
    }

    public void setIsBlackList(String isBlackList) {
        this.isBlackList = isBlackList;
    }

    public String getSaudaValidityPeriod() {
        return mSaudaValidityPeriod;
    }

    public void setSaudaValidityPeriod(String saudavalidityperiod) {
        this.mSaudaValidityPeriod = saudavalidityperiod;
    }

    public String getCheckFlag() {
        return mCheckFlag;
    }

    public void setCheckFlag(String checkFlag) {
        this.mCheckFlag = checkFlag;
    }

    public String getRouteName() {
        return mRouteName;
    }

    public void setRouteName(String routeName) {
        this.mRouteName = routeName;
    }

    public String getMinimumStock() {
        return minimumStock;
    }

    public void setMinimumStock(String minimumStock) {
        this.minimumStock = minimumStock;
    }

    public String getImage() {
        return image;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public String getBranchCode() {
        return branchCode;
    }

    public void setBranchCode(String branchCode) {
        this.branchCode = branchCode;
    }

    public String getVisitDay() {
        return visitDay;
    }

    public void setVisitDay(String visitDay) {
        this.visitDay = visitDay;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getTaggedCustomerCode() {
        return TaggedCustomerCode;
    }

    public void setTaggedCustomerCode(String TaggedCustomerCode) {
        this.TaggedCustomerCode = TaggedCustomerCode;
    }

    public String getSaudaLimit() {
        return sauda_limit;
    }

    public void setSaudaLimit(String sauda_limit) {
        this.sauda_limit = sauda_limit;
    }

    public String getPendingQty() {
        return pending_qty;
    }

    public void setPendingQty(String pending_qty) {
        this.pending_qty = pending_qty;
    }

    public String getIncoTerms() {
        return IncoTerms;
    }

    public void setIncoTerms(String IncoTerms) {
        this.IncoTerms = IncoTerms;
    }

    public String getLoadabilityTon() {
        return LoadabilityTon;
    }

    public void setLoadabilityTon(String LoadabilityTon) {
        this.LoadabilityTon = LoadabilityTon;
    }

    public String getTransportMode() {
        return TransportMode;
    }

    public void setTransportMode(String TransportMode) {
        this.TransportMode = TransportMode;
    }

    public String getstate() {
        return state;
    }

    public void setstate(String state) {
        this.state = state;
    }

    public String getSaudaType() {
        return saudaType;
    }

    public void setSaudaType(String saudaType) {
        this.saudaType = saudaType;
    }

    public String getzone() {
        return zone;
    }

    public void setzone(String zone) {
        this.zone = zone;
    }

    public String getVisitSequence() {
        return visitSequence;
    }

    public void setVisitSequence(String visitSequence) {
        this.visitSequence = visitSequence;
    }

    public String getactivated() {
        return activated;
    }

    public void setactivated(String activated) {
        this.activated = activated;
    }

    public String getactivated_customer_code() {
        return activated_customer_code;
    }

    public void setactivated_customer_code(String activated_customer_code) {
        this.activated_customer_code = activated_customer_code;
    }

    public String getbase_latt() {
        return base_latt;
    }

    public void setbase_latt(String base_latt) {
        this.base_latt = base_latt;
    }

    public String getbase_longi() {
        return base_longi;
    }

    public void setbase_longi(String base_longi) {
        this.base_longi = base_longi;
    }

    public String getretailer_app() {
        return retailer_app;
    }

    public void setretailer_app(String retailer_app) {
        this.retailer_app = retailer_app;
    }

    public String getneed_location_update() {
        return need_location_update;
    }

    public void setneed_location_update(String need_location_update) {
        this.need_location_update = need_location_update;
    }

    public Double getFreightCharge() {
        return freightCharge;
    }

    public void setFreightCharge(Double freightCharge) {
        this.freightCharge = freightCharge;
    }

    public String getcustomerImage() {
        return customerImage;
    }

    public void setcustomerImage(String customerImage) {
        this.customerImage = customerImage;
    }

    public String getciLogic() {
        return ciLogic;
    }

    public void setciLogic(String ciLogic) {
        this.ciLogic = ciLogic;
    }

    public int getciLogicPriority() {
        return ciLogicPriority;
    }

    public void setciLogicPriority(int ciLogicPriority) {
        this.ciLogicPriority = ciLogicPriority;
    }

    public String getcategoryOfStore() {
        return categoryOfStore;
    }

    public void setcategoryOfStore(String categoryOfStore) {
        this.categoryOfStore = categoryOfStore;
    }

    public String getinStoreActivityPossible() {
        return inStoreActivityPossible;
    }

    public void setinStoreActivityPossible(String inStoreActivityPossible) {
        this.inStoreActivityPossible = inStoreActivityPossible;
    }

    public String getIsNewCustomer() {
        return isNewCustomer;
    }

    public void setIsNewCustomer(String isNewCustomer) {
        this.isNewCustomer = isNewCustomer;
    }

    public String getoutletImage() {
        return outletImage;
    }

    public void setoutletImage(String outletImage) {
        this.outletImage = outletImage;
    }

    public String getownerImage() {
        return ownerImage;
    }

    public void setownerImage(String ownerImage) {
        this.ownerImage = ownerImage;
    }

    public String getgstImage() {
        return gstImage;
    }

    public void setgstImage(String gstImage) {
        this.gstImage = gstImage;
    }

    public String getadharImage() {
        return adharImage;
    }

    public void setadharImage(String adharImage) {
        this.adharImage = adharImage;
    }

    public String getfirmName() {
        return firmName;
    }

    public void setfirmName(String fisrmName) {
        this.firmName = fisrmName;
    }

    public void setpropName(String propName) {
        this.propName = propName;
    }

    public String getgstNo() {
        return gstNo;
    }

    public void setgstNo(String gstNo) {
        this.gstNo = gstNo;
    }

    public String getadharNo() {
        return adharNo;
    }

    public void setadharNo(String adharNo) {
        this.adharNo = adharNo;
    }

    public String getDrCategory() {
        return drCategory;
    }

    public void setDrCategory(String drCategory) {
        this.drCategory = drCategory;
    }
}
