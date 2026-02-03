package com.forcepower.acedns.bean;

public class ProductMasterDetails {
    boolean amtEntered = false, QuantityEligibleForTD = false,
            isTd = true, schemePresent = false;
    int selectedUOM = -1;
    String slNo = "", prodCode = "", grpCode = "", subGrpCode = "", brndCode = "", desc = "", isAcedns = "",
            isBlkLst = "", closingStk = "0", grpName = "", subGrpName = "", brndName = "", uom1 = "", uom2 = "",
            UomSelectedForProduct = "", qty = "0", stkQty = "0", qtyRemaining = "0", mrpCode = "", mrpValue = "0.00",
            tradeDiscnt = "0.00", tradeDiscntLimit = "0.00", vatVAlue = "0", despatchQtyForStockIn = "",
            statusForStockIn = "", allocation_id = "", allocation_qty = "", allocation_from_date = "",
            allocation_to_date = "", requisition_id = "", allocationDate = "", RequisitionDate = "", stockOutQty = "",
            activeFlag = "", saudaNo = "", amount = "", IMEINo = "", conversionFactor = "", packSize = "", freight = "",
            primaryFreight = "", depotCost = "", marginCost = "", filterCode = "", mUOM3 = "", mConversionFactorTwo = "",
            mTD = "", mPremium = "0", mBranchCode = "", mVerticalValue = "", mSecondaryUnit = "", dnsProdCode = "",
            focus = "", weightage = "", vatRate = "", additionalVatRate = "", FreightCost = "", plan = "", purchase = "",
            tdPercent = "", SchemeIds = "", msl = "", indicativePrice = "", packUnit = "",
            freeBieUom = "", freebieFilter = "", allocatedQty = "", customerCode = "", customerName = "",
            customerRds = "", size = "", billedQty = "", openingStock = "", stockOutQtyOthers = "", stockOutQtyTotal = "",
            customerPhone = "", unregisteredStockOut = "", unregisteredStockOutTotal = "", stockReturnReason = "",
            rate = "", basicRate = "", parentProdCode = "", parentProdName = "", totalBargainQuantity = "",
            mfgDate = "", brokerageCost = "", brokerageCostSS = "", uom4 = "", uom5 = "", addFreight = "",
            minusFreight = "", additionalPremium = "", additionalTD = "", stock = "", order_wise_remarks = "",
            weidth = "", height = "";

    public String getslNo() {
        return slNo;
    }

    public void setslNo(String slNo) {
        this.slNo = slNo;
    }

    public String getDnsProdCode() {
        return dnsProdCode;
    }

    public void setDnsProdCode(String dnsProdCode) {
        this.dnsProdCode = dnsProdCode;
    }

    public synchronized String getFilterCode() {
        return filterCode;
    }

    public String getFreight() {
        return freight;
    }

    public void setFreight(String freight) {
        this.freight = freight;
    }

    public String getPackSize() {
        return packSize;
    }

    public void setPackSize(String packSize) {
        this.packSize = packSize;
    }

    public int getSelectedUOM() {
        return selectedUOM;
    }

    public void setSelectedUOM(int selectedUOM) {
        this.selectedUOM = selectedUOM;
    }

    public String getConversionFactor() {
        return conversionFactor;
    }

    public void setConversionFactor(String conversionFactor) {
        this.conversionFactor = conversionFactor;
    }

    public String getIMEINo() {
        return IMEINo;
    }

    public void setIMEINo(String iMEINo) {
        IMEINo = iMEINo;
    }

    public boolean isAmtEntered() {
        return amtEntered;
    }

    public void setAmtEntered(boolean amtEntered) {
        this.amtEntered = amtEntered;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }

    public String getDespatchQtyForStockIn() {
        return despatchQtyForStockIn;
    }

    public void setDespatchQtyForStockIn(String despatchQtyForStockIn) {
        this.despatchQtyForStockIn = despatchQtyForStockIn;
    }

    public String getStatusForStockIn() {
        return statusForStockIn;
    }

    public void setStatusForStockIn(String statusForStockIn) {
        this.statusForStockIn = statusForStockIn;
    }

    public String getVat() {
        return vatVAlue;
    }

    public void setVat(String vatVAlue) {
        this.vatVAlue = vatVAlue;
    }

    public String getUom1() {
        return uom1;
    }

    public void setUom1(String uom1) {
        this.uom1 = uom1;
    }

    public String getUomSelectedForProduct() {
        return UomSelectedForProduct;
    }

    public void setUomSelectedForProduct(String UomSelectedForProduct) {
        this.UomSelectedForProduct = UomSelectedForProduct;
    }

    public String getUom2() {
        return uom2;
    }

    public void setUom2(String uom2) {
        this.uom2 = uom2;
    }

    public String getGrpName() {
        return grpName;
    }

    public void setGrpName(String grpName) {
        this.grpName = grpName;
    }

    public String getSubGrpName() {
        return subGrpName;
    }

    public void setSubGrpName(String subGrpName) {
        this.subGrpName = subGrpName;
    }

    public String getBrndName() {
        return brndName;
    }

    public void setBrndName(String brndName) {
        this.brndName = brndName;
    }

    public final String getMrpValue() {
        return mrpValue;
    }

    public final void setMrpValue(String mrpValue) {
        this.mrpValue = mrpValue;
    }

    public String getQty() {
        return qty;
    }

    public void setQty(String qty) {
        this.qty = qty;
    }

    public String getQtyRemaining() {
        return qtyRemaining;
    }

    public void setQtyRemaining(String qtyRemaining) {
        this.qtyRemaining = qtyRemaining;
    }

    public String getMrpCode() {
        return mrpCode;
    }

    public void setMrpCode(String mrpCode) {
        this.mrpCode = mrpCode;
    }

    public String getTradeDiscnt() {
        return tradeDiscnt;
    }

    public void setTradeDiscnt(String tradeDiscnt) {
        this.tradeDiscnt = tradeDiscnt;
    }

    public String gettradeDiscntLimit() {
        return tradeDiscntLimit;
    }

    public void settradeDiscntLimit(String tradeDiscntLimit) {
        this.tradeDiscntLimit = tradeDiscntLimit;
    }

    public String getClosingStk() {
        return closingStk;
    }

    public void setClosingStk(String closingStk) {
        this.closingStk = closingStk;
    }

    public String getGrpCode() {
        return grpCode;
    }

    public void setGrpCode(String grpCode) {
        this.grpCode = grpCode;
    }

    public String getSubGrpCode() {
        return subGrpCode;
    }

    public void setSubGrpCode(String subGrpCode) {
        this.subGrpCode = subGrpCode;
    }

    public String getBrndCode() {
        return brndCode;
    }

    public void setBrndCode(String brndCode) {
        this.brndCode = brndCode;
    }

    public String getProdCode() {
        return prodCode;
    }

    public void setProdCode(String prodCode) {
        this.prodCode = prodCode;
    }

    public String getDesc() {
        return desc;
    }

    public void setDesc(String desc) {
        this.desc = desc;
    }

    public String getIsAcedns() {
        return isAcedns;
    }

    public void setIsAcedns(String isAcedns) {
        this.isAcedns = isAcedns;
    }

    public String getIsBlkLst() {
        return isBlkLst;
    }

    public void setIsBlkLst(String isBlkLst) {
        this.isBlkLst = isBlkLst;
    }

    public String getUOM3() {
        return mUOM3;
    }

    public void setUOM3(String uom3) {
        this.mUOM3 = uom3;
    }

    public String getConversionFactorTwo() {
        return mConversionFactorTwo;
    }

    public void setConversionFactorTwo(String conversionfactortwo) {
        this.mConversionFactorTwo = conversionfactortwo;
    }

    public String getTD() {
        return mTD;
    }

    public void setTD(String td) {
        this.mTD = td;
    }

    public String getPremium() {
        return mPremium;
    }

    public void setPremium(String premium) {
        this.mPremium = premium;
    }

    public String getBranchCode() {
        return mBranchCode;
    }

    public void setBranchCode(String branchcode) {
        this.mBranchCode = branchcode;
    }

    public String getVerticalValue() {
        return mVerticalValue;
    }

    public void setVerticalValue(String verticalValue) {
        this.mVerticalValue = verticalValue;
    }

    public String getSecondaryUnit() {
        return mSecondaryUnit;
    }

    public void setSecondaryUnit(String secondaryUnit) {
        this.mSecondaryUnit = secondaryUnit;
    }

    public boolean getIsTradeDiscount() {
        return isTd;
    }

    public void setIsTradeDiscount(boolean td) {
        this.isTd = td;
    }

    public boolean getSchemePresent() {
        return schemePresent;
    }

    public void setSchemePresent(boolean schemePresent) {
        this.schemePresent = schemePresent;
    }

    public String getFocus() {
        return focus;
    }

    public void setFocus(String focus) {
        this.focus = focus;
    }

    public String getWeightage() {
        return weightage;
    }

    public void setWeightage(String weightage) {
        this.weightage = weightage;
    }

    public String getVatRate() {
        return vatRate;
    }

    public void setVatRate(String vatRate) {
        this.vatRate = vatRate;
    }

    public String getAdditionalVatRate() {
        return additionalVatRate;
    }

    public void setAdditionalVatRate(String additionalVatRate) {
        this.additionalVatRate = additionalVatRate;
    }

    public String getFreightCost() {
        return FreightCost;
    }

    public void setFreightCost(String FreightCost) {
        this.FreightCost = FreightCost;
    }

    public String getPlan() {
        return plan;
    }

    public void setPlan(String plan) {
        this.plan = plan;
    }

    public String getPurchase() {
        return purchase;
    }

    public void setPurchase(String purchase) {
        this.purchase = purchase;
    }

    public boolean getQuantityEligibleForTD() {
        return QuantityEligibleForTD;
    }

    public void setQuantityEligibleForTD(boolean AmountEligibleForTD) {
        this.QuantityEligibleForTD = AmountEligibleForTD;
    }

    public String getTDPercent() {
        return tdPercent;
    }

    public void setTDPercent(String tdPercent) {
        this.tdPercent = tdPercent;
    }

    public String getSchemeIds() {
        return SchemeIds;
    }

    public void setSchemeIds(String SchemeIds) {
        this.SchemeIds = SchemeIds;
    }

    public String getmsl() {
        return msl;
    }

    public void setmsl(String msl) {
        this.msl = msl;
    }

    public String getallocation_id() {
        return allocation_id;
    }

    public void setallocation_id(String allocation_id) {
        this.allocation_id = allocation_id;
    }

    public String getallocation_qty() {
        return allocation_qty;
    }

    public void setallocation_qty(String allocation_qty) {
        this.allocation_qty = allocation_qty;
    }

    public void setallocation_from_date(String allocation_from_date) {
        this.allocation_from_date = allocation_from_date;
    }

    public void setallocation_to_date(String allocation_to_date) {
        this.allocation_to_date = allocation_to_date;
    }

    public String getrequisition_id() {
        return requisition_id;
    }

    public void setrequisition_id(String requisition_id) {
        this.requisition_id = requisition_id;
    }

    public String getindicativePrice() {
        return indicativePrice;
    }

    public String getpackUnit() {
        return packUnit;
    }

    public void setpackUnit(String packUnit) {
        this.packUnit = packUnit;
    }

    public String getfreeBieUom() {
        return freeBieUom;
    }

    public void setfreeBieUom(String freeBieUom) {
        this.freeBieUom = freeBieUom;
    }

    public String getStkQty() {
        return stkQty;
    }

    public void setStkQty(String stkQty) {
        this.stkQty = stkQty;
    }

    public String getfreebieFilter() {
        return freebieFilter;
    }

    public void setfreebieFilter(String freebieFilter) {
        this.freebieFilter = freebieFilter;
    }

    public String getallocatedQty() {
        return allocatedQty;
    }

    public void setallocatedQty(String allocatedQty) {
        this.allocatedQty = allocatedQty;
    }

    public String getcustomerCode() {
        return customerCode;
    }

    public void setcustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public String getCustomerName() {
        return customerName;
    }

    public void setCustomerName(String customerName) {
        this.customerName = customerName;
    }

    public String getcustomerRds() {
        return customerRds;
    }

    public void setcustomerRds(String customerRds) {
        this.customerRds = customerRds;
    }

    public String getsize() {
        return size;
    }

    public void setsize(String size) {
        this.size = size;
    }

    public String getallocationDate() {
        return allocationDate;
    }

    public void setallocationDate(String allocationDate) {
        this.allocationDate = allocationDate;
    }

    public String getRequisitionDate() {
        return RequisitionDate;
    }

    public void setRequisitionDate(String RequisitionDate) {
        this.RequisitionDate = RequisitionDate;
    }

    public String getbilledQty() {
        return billedQty;
    }

    public void setbilledQty(String billedQty) {
        this.billedQty = billedQty;
    }

    public String getstockOutQty() {
        return stockOutQty;
    }

    public void setstockOutQty(String stockOutQty) {
        this.stockOutQty = stockOutQty;
    }

    public String getactiveFlag() {
        return activeFlag;
    }

    public void setactiveFlag(String activeFlag) {
        this.activeFlag = activeFlag;
    }

    public String getOpeningStock() {
        return openingStock;
    }

    public void setOpeningStock(String openingStock) {
        this.openingStock = openingStock;
    }

    public void setstockOutQtyOthers(String stockOutQtyOthers) {
        this.stockOutQtyOthers = stockOutQtyOthers;
    }

    public void setstockOutQtyTotal(String stockOutQtyTotal) {
        this.stockOutQtyTotal = stockOutQtyTotal;
    }

    public String getcustomerPhone() {
        return customerPhone;
    }

    public void setcustomerPhone(String customerPhone) {
        this.customerPhone = customerPhone;
    }

    public String getunregisteredStockOut() {
        return unregisteredStockOut;
    }

    public void setunregisteredStockOut(String unregisteredStockOut) {
        this.unregisteredStockOut = unregisteredStockOut;
    }

    public void setunregisteredStockOutTotal(String unregisteredStockOutTotal) {
        this.unregisteredStockOutTotal = unregisteredStockOutTotal;
    }

    public String getstockReturnReason() {
        return stockReturnReason;
    }

    public void setstockReturnReason(String stockReturnReason) {
        this.stockReturnReason = stockReturnReason;
    }

    public String getRate() {
        return rate;
    }

    public void setRate(String rate) {
        this.rate = rate;
    }

    public void setBasicRate(String basicRate) {
        this.basicRate = basicRate;
    }

    public String getparentProdCode() {
        return parentProdCode;
    }

    public void setparentProdCode(String parentProdCode) {
        this.parentProdCode = parentProdCode;
    }

    public void setparentProdName(String parentProdName) {
        this.parentProdName = parentProdName;
    }

    public String gettotalBargainQuantity() {
        return totalBargainQuantity;
    }

    public void settotalBargainQuantity(String totalBargainQuantity) {
        this.totalBargainQuantity = totalBargainQuantity;
    }

    public String getmfgDate() {
        return mfgDate;
    }

    public void setmfgDate(String mfgDate) {
        this.mfgDate = mfgDate;
    }

    public String getbrokerageCost() {
        return brokerageCost;
    }

    public void setbrokerageCost(String brokerageCost) {
        this.brokerageCost = brokerageCost;
    }

    public String getbrokerageCostSS() {
        return brokerageCostSS;
    }

    public void setbrokerageCostSS(String brokerageCostSS) {
        this.brokerageCostSS = brokerageCostSS;
    }

    public String getuom4() {
        return uom4;
    }

    public void setuom4(String uom4) {
        this.uom4 = uom4;
    }

    public String getuom5() {
        return uom5;
    }

    public void setuom5(String uom5) {
        this.uom5 = uom5;
    }

    public String getsaudaNo() {
        return saudaNo;
    }

    public void setsaudaNo(String saudaNo) {
        this.saudaNo = saudaNo;
    }

    public String getaddFreight() {
        return addFreight;
    }

    public void setaddFreight(String addFreight) {
        this.addFreight = addFreight;
    }

    public String getminusFreight() {
        return minusFreight;
    }

    public void setminusFreight(String minusFreight) {
        this.minusFreight = minusFreight;
    }

    public String getadditionalPremium() {
        return additionalPremium;
    }

    public void setadditionalPremium(String additionalPremium) {
        this.additionalPremium = additionalPremium;
    }

    public String getadditionalTD() {
        return additionalTD;
    }

    public void setadditionalTD(String additionalTD) {
        this.additionalTD = additionalTD;
    }

    public String getprimaryFreight() {
        return primaryFreight;
    }

    public void setprimaryFreight(String primaryFreight) {
        this.primaryFreight = primaryFreight;
    }

    public String getdepotCost() {
        return depotCost;
    }

    public void setdepotCost(String depotCost) {
        this.depotCost = depotCost;
    }

    public String getmarginCost() {
        return marginCost;
    }

    public void setmarginCost(String marginCost) {
        this.marginCost = marginCost;
    }

    public String getStock() {
        return stock;
    }

    public void setStock(String stock) {
        this.stock = stock;
    }

    public String getOrder_wise_remarks() {
        return order_wise_remarks;
    }

    public void setOrder_wise_remarks(String order_wise_remarks) {
        this.order_wise_remarks = order_wise_remarks;
    }

    public String getWeidth() {
        return weidth;
    }

    public void setWeidth(String weidth) {
        this.weidth = weidth;
    }

    public String getHeight() {
        return height;
    }

    public void setHeight(String height) {
        this.height = height;
    }
}