from flask import Flask, render_template, url_for, redirect

app = Flask(__name__)

@app.route("/", methods=['GET'])
def home():
    return render_template("home.html")

if __name__ == '__main__':
    app.run(host="192.168.1.38",port=9000,debug=True)