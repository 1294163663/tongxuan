/**
 * Created by ycy on 17-7-20.
 */
function vailIdCard(idCard_NO){
    var reg18 = new RegExp(/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/);//6位地址码+8位出生日期+3位顺序码+1位校验码
    var reg15 = new RegExp(/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/);//6位地址码+6位出生日期+3位顺序码
    console.log(idCard_NO.match(reg18));
    if(idCard_NO.match(reg18) || idCard_NO.match(reg15)){
        console.log('身份证编号符合规则');
        return true;
    }
    console.log('身份证编号不符合规则');
    alert('身份证编号不符合规则');
    return false;
}