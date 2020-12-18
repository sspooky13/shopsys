export default class Register {

    /**
     * @returns {Register}
     */
    constructor () {

        Register.CALL_PRIORITY_NORMAL = 500;
        Register.CALL_PRIORITY_HIGH = 300;

        if (Register.instance) {
            return Register.instance;
        }

        Register.instance = this;
        this.callbackQueue = [];

        return this;
    }

    /**
     * @param {Object} callback
     * @param {number|string} arg1
     * @param {number|string=} arg2
     */
    registerCallback (callback, arg1, arg2) {

        let callPriority = Register.CALL_PRIORITY_NORMAL;
        let callbackName = null;

        if (arg1) {
            if (typeof arg1 === 'number') {
                callPriority = arg1;
            }
            if (typeof arg1 === 'string') {
                callbackName = arg1;
            }
        }

        if (arg2) {
            if (typeof arg2 === 'number') {
                callPriority = arg2;
            }
            if (typeof arg2 === 'string') {
                callbackName = arg2;
            }
        }

        this.callbackQueue.push({
            callbackName,
            callPriority,
            callback: callback
        });
    }

    /**
     * @param {string} callbackName
     * @param {Object} newCallback
     */
    replaceCallback (callbackName, newCallback) {
        this.callbackQueue = this.callbackQueue.map(callbackItem => {
            if (callbackItem.callbackName === callbackName) {
                return {
                    ...callbackItem,
                    callback: newCallback
                };
            }

            return callbackItem;
        });
    }

    /**
     * @param {string} callbackName
     */
    removeCallback (callbackName) {
        this.callbackQueue = this.callbackQueue.filter(callbackItem => callbackItem.callbackName !== callbackName);
    }

    /**
     * @param {jQuery} $container
     */
    registerNewContent ($container) {
        this.callbackQueue.sort(function (a, b) {
            return a.callPriority - b.callPriority;
        });

        for (let i in this.callbackQueue) {
            this.callbackQueue[i].callback($container);
        }
    }
}
