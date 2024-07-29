from flask import Flask, request, jsonify, send_file
from flask_cors import CORS
import pandas as pd
import numpy as np
import fasttext
import os
import matplotlib.pyplot as plt
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

UPLOAD_FOLDER = 'uploads'
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

# Ensure the uploads directory exists
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

# Function to load fastText model
def load_fastText_model(model_file):
    model = fasttext.load_model(model_file)
    return model

# Function to predict bullying percentage for a dataset of tweets
def predict_bullying_percentage(model, dataset_file):
    # Load dataset
    df_dataset = pd.read_csv(dataset_file)

    # Extract tweets from dataset
    tweets = df_dataset['text_message']

    # Remove newline characters from tweets
    tweets = tweets.str.replace('\n', '')

    # Predict labels for each tweet in the dataset
    labels = [model.predict(tweet)[0][0] for tweet in tweets]

    # Convert labels to binary (1 for bullying, 0 for non-bullying)
    labels_binary = [1 if label == '__label__1' else 0 for label in labels]

    # Calculate percentage of bullying content
    bullying_percentage = np.mean(labels_binary) * 100
    non_bullying_percentage = 100 - bullying_percentage

    return bullying_percentage, non_bullying_percentage

# Function to generate pie chart and save as an image
def generate_pie_chart(bullying_percentage, non_bullying_percentage, output_path):
    labels = 'Bullying', 'Non-Bullying'
    sizes = [bullying_percentage, non_bullying_percentage]
    colors = ['red', 'green']
    explode = (0.1, 0)  # explode 1st slice

    plt.figure(figsize=(6, 6))
    plt.pie(sizes, explode=explode, labels=labels, colors=colors,
            autopct='%1.1f%%', shadow=True, startangle=140)
    plt.axis('equal')  # Equal aspect ratio ensures that pie is drawn as a circle.
    plt.title('Cyber Bullying Analysis')
    plt.savefig(output_path)
    plt.close()

# Function to generate PDF report
def generate_pdf_report(bullying_percentage, non_bullying_percentage, chart_path, output_path):
    pdf = canvas.Canvas(output_path, pagesize=letter)
    width, height = letter

    pdf.setTitle("Cyber Bullying Analysis Report")

    pdf.setFont("Helvetica-Bold", 16)
    pdf.drawCentredString(width / 2.0, height - 40, "Cyber Bullying Analysis Report")

    pdf.setFont("Helvetica", 12)
    pdf.drawString(50, height - 80, f"Bullying Percentage: {bullying_percentage:.2f}%")
    pdf.drawString(50, height - 100, f"Non-Bullying Percentage: {non_bullying_percentage:.2f}%")

    pdf.drawImage(chart_path, 50, height - 400, width=500, height=300)

    pdf.save()

# Route to handle file upload and prediction
@app.route('/upload', methods=['POST'])
def upload_file():
    try:
        if 'file' not in request.files:
            return jsonify({'error': 'No file part in the request'}), 400

        file = request.files['file']
        if file.filename == '':
            return jsonify({'error': 'No selected file'}), 400

        if file:
            filepath = os.path.join(app.config['UPLOAD_FOLDER'], 'data.csv')
            file.save(filepath)

            # Load pre-trained fastText model
            model_file = 'classifier_updated.bin'  # Ensure this is the correct path to your model file
            model = load_fastText_model(model_file)

            # Predict bullying percentage
            bullying_percentage, non_bullying_percentage = predict_bullying_percentage(model, filepath)

            # Generate pie chart
            chart_path = os.path.join(app.config['UPLOAD_FOLDER'], 'chart.png')
            generate_pie_chart(bullying_percentage, non_bullying_percentage, chart_path)

            # Generate PDF report
            pdf_path = os.path.join(app.config['UPLOAD_FOLDER'], 'report.pdf')
            generate_pdf_report(bullying_percentage, non_bullying_percentage, chart_path, pdf_path)

            return send_file(pdf_path, as_attachment=True, download_name='report.pdf')

    except Exception as e:
        print(f"Error: {e}")
        return jsonify({'error': 'File upload failed', 'message': str(e)}), 500

    return jsonify({'error': 'Unknown error occurred'}), 500

if __name__ == "__main__":
    app.run(debug=True)