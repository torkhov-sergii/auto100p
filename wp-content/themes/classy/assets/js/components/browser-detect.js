//Use for detect current browser

const browserDetect = () => {

    let a;
    if (navigator.userAgent.search(/Safari/) > 0) {a = 'Safari'}
    if (navigator.userAgent.search(/Firefox/) > 0) {a = 'MozillaFirefox'}
    if (navigator.userAgent.search(/MSIE/) > 0 || navigator.userAgent.search(/NET CLR /) > 0) {a = 'InternetExplorer'}
    if (navigator.userAgent.search(/Chrome/) > 0) {a = 'Google Chrome'}
    if (navigator.userAgent.search(/YaBrowser/) > 0) {a = 'Yandex'}
    if (navigator.userAgent.search(/OPR/) > 0) {a = 'Opera'}
    if (navigator.userAgent.search(/Konqueror/) > 0) {a = 'Konqueror'}
    if (navigator.userAgent.search(/Iceweasel/) > 0) {a = 'DebianIceweasel'}
    if (navigator.userAgent.search(/SeaMonkey/) > 0) {a = 'SeaMonkey'}
    if (navigator.userAgent.search(/Edge/) > 0) {a = 'MicrosoftEdge'}
    return a;

};

export default browserDetect;