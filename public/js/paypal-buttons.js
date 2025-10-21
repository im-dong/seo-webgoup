/**
 * PayPal Smart Buttons - 独立按钮实现
 * 支持PayPal账户和信用卡分离
 */

class PayPalSmartButtons {
    constructor() {
        this.clientId = PAYPAL_CLIENT_ID;
        this.currency = 'USD';
        this.isSandbox = PAYPAL_SANDBOX;
        this.isReady = false;
    }

    /**
     * 初始化PayPal SDK
     */
    async init() {
        if (this.isReady) return;

        return new Promise((resolve, reject) => {
            // 动态加载PayPal SDK
            const script = document.createElement('script');
            script.src = `https://www.paypal.com/sdk/js?client-id=${this.clientId}&currency=${this.currency}${this.isSandbox ? '&buyer-country=US' : ''}`;
            script.onload = () => {
                this.isReady = true;
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * 渲染PayPal按钮
     */
    async renderPayPalButton(container, serviceId, options = {}) {
        await this.init();

        if (!window.paypal) {
            throw new Error('PayPal SDK not loaded');
        }

        const defaultOptions = {
            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'rect',
                label: 'paypal'
            },
            createOrder: (data, actions) => this.createOrder(serviceId),
            onApprove: (data, actions) => this.onApprove(data, serviceId),
            onError: (err) => this.onError(err),
            onCancel: (data) => this.onCancel(data)
        };

        const finalOptions = { ...defaultOptions, ...options };

        return window.paypal.Buttons(finalOptions).render(container);
    }

    /**
     * 渲染信用卡按钮
     */
    async renderCardButton(container, serviceId, options = {}) {
        await this.init();

        if (!window.paypal) {
            throw new Error('PayPal SDK not loaded');
        }

        const defaultOptions = {
            style: {
                layout: 'vertical',
                color: 'gold',
                shape: 'rect',
                label: 'pay'
            },
            fundingSource: window.paypal.FUNDING.CARD,
            createOrder: (data, actions) => this.createOrder(serviceId),
            onApprove: (data, actions) => this.onApprove(data, serviceId),
            onError: (err) => this.onError(err),
            onCancel: (data) => this.onCancel(data)
        };

        const finalOptions = { ...defaultOptions, ...options };

        return window.paypal.Buttons(finalOptions).render(container);
    }

    /**
     * 创建PayPal订单
     */
    async createOrder(serviceId) {
        try {
            const response = await fetch(`${URLROOT}/orders/createApi/${serviceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Failed to create order');
            }

            return data.id; // PayPal订单ID
        } catch (error) {
            console.error('Create order error:', error);
            this.showError('创建订单失败：' + error.message);
            throw error;
        }
    }

    /**
     * 支付批准处理
     */
    async onApprove(data, serviceId) {
        this.showLoading('正在处理支付...');

        try {
            // 获取内部订单ID (从PayPal订单ID获取)
            const orderResponse = await fetch(`${URLROOT}/orders/getInternalByPaypal/${data.orderID}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!orderResponse.ok) {
                throw new Error('Failed to get internal order');
            }

            const orderData = await orderResponse.json();
            const internalOrderId = orderData.order_id;

            // 捕获支付
            const response = await fetch(`${URLROOT}/orders/capture/${internalOrderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    orderID: data.orderID
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Payment failed');
            }

            this.showSuccess('支付成功！正在跳转...');

            // 跳转到订单详情页
            setTimeout(() => {
                window.location.href = `${URLROOT}/orders/details/${internalOrderId}`;
            }, 2000);

        } catch (error) {
            console.error('Capture error:', error);
            this.showError('支付处理失败：' + error.message);
        }
    }

    /**
     * 错误处理
     */
    onError(err) {
        console.error('PayPal error:', err);
        this.showError('支付过程出现错误，请重试');
    }

    /**
     * 取消处理
     */
    onCancel(data) {
        console.log('Payment cancelled:', data);
        this.showInfo('支付已取消');
    }

    /**
     * 显示加载状态
     */
    showLoading(message) {
        // 移除之前的消息
        this.removeMessages();

        const div = document.createElement('div');
        div.className = 'alert alert-info';
        div.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${message}`;
        div.id = 'paypal-message';

        const container = document.querySelector('.paypal-buttons-container');
        if (container) {
            container.parentNode.insertBefore(div, container);
        }
    }

    /**
     * 显示成功消息
     */
    showSuccess(message) {
        this.showMessage(message, 'success');
    }

    /**
     * 显示错误消息
     */
    showError(message) {
        this.showMessage(message, 'danger');
    }

    /**
     * 显示信息消息
     */
    showInfo(message) {
        this.showMessage(message, 'info');
    }

    /**
     * 显示消息
     */
    showMessage(message, type) {
        this.removeMessages();

        const div = document.createElement('div');
        div.className = `alert alert-${type}`;
        div.innerHTML = message;
        div.id = 'paypal-message';

        const container = document.querySelector('.paypal-buttons-container');
        if (container) {
            container.parentNode.insertBefore(div, container);
        }

        // 自动隐藏消息
        if (type === 'success' || type === 'info') {
            setTimeout(() => this.removeMessages(), 5000);
        }
    }

    /**
     * 移除所有消息
     */
    removeMessages() {
        const messages = document.querySelectorAll('#paypal-message');
        messages.forEach(msg => msg.remove());
    }
}

// 全局实例
window.paypalButtons = new PayPalSmartButtons();