[//]: # (wakatime-stats)
[//]: # (end-wakatime-stats)


# Wakatime Stats GitHub Actions

**Author:** Clifford Laserre

## Overview

This GitHub Actions workflow is designed to update a README file with your Wakatime statistics. It utilizes a GitHub access token, your Wakatime API key, and your Wakatime user ID to fetch and display your coding activity on your repository's README file.

## Prerequisites

Before you can use this workflow, you need to ensure you have the following:

1. **GitHub Access Token**:
    - You should have a GitHub access token with the `repo` scope. If you don't have one, you can create it in your GitHub account settings.

2. **Wakatime API Key**:
    - You need your Wakatime API key, which you can obtain from your Wakatime account settings.

3. **Wakatime User ID**:
    - Your Wakatime User ID is required to fetch your coding statistics. You can find it in your Wakatime account settings.

## Usage

To set up and use this GitHub Actions workflow in your repository, follow these steps:

1. **Create Workflow File**:
    - Create a new GitHub Actions workflow file in your repository. For example, you can create a `.github/workflows/wakatime-stats.yml` file.

2. **Configure Workflow**:
    - In the workflow file, specify the following input parameters:
        - `GH_TOKEN`: Your GitHub access token.
        - `WAKATIME_API_KEY`: Your Wakatime API Key.
        - `WAKATIME_USER_ID`: Your Wakatime User ID.

    - Example:
      ```yaml
      name: Wakatime Stats
 
      on:
        push:
          branches:
            - main
 
      jobs:
        update-readme:
          runs-on: ubuntu-latest
 
          steps:
            - name: Checkout Code
              uses: actions/checkout@v2
 
            - name: Update README with Wakatime Stats
              env:
                GH_TOKEN: ${{ secrets.GH_TOKEN }}
                WAKATIME_API_KEY: ${{ secrets.WAKATIME_API_KEY }}
                WAKATIME_USER_ID: ${{ secrets.WAKATIME_USER_ID }}
              run: |
                # Add the script or commands to update your README with Wakatime stats here.
      ```

3. **Secrets**:
    - Store your GitHub access token, Wakatime API Key, and Wakatime User ID as secrets in your GitHub repository settings. You can access these secrets as shown in the example workflow above.

4. **Customize Workflow**:
    - Customize the script or commands inside the workflow to fetch Wakatime stats and update your README file.

5. **Run Workflow**:
    - The workflow will be triggered when you push changes to your repository's main branch, as per the example configuration. You can customize the trigger events as needed.

## License

This GitHub Actions workflow is provided under the [MIT License](LICENSE).
