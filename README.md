[//]: # (wakatime-stats)
[//]: # (end-wakatime-stats)


# Wakatime Stats GitHub Actions


## Overview

This GitHub Actions workflow is designed to update a README file with your Wakatime statistics. It utilizes a GitHub access token, your Wakatime API key, and your Wakatime user ID to fetch and display your coding activity on your repository's README file.

## Prerequisites

Before you can use this workflow, you need to ensure you have the following:



1. **Wakatime Account** :
   - [WakaTime](https://wakatime.com/) is a developer productivity tool and time tracking service specifically designed for software developers. 
   It helps developers keep track of how much time they spend working on various programming tasks, coding projects, 
   and programming languages.

2. **Wakatime API Key** :
   - You need your Wakatime API key, which you can obtain from your Wakatime account settings.

3. **Wakatime User ID** :
   - Your Wakatime User ID is required to fetch your coding statistics. 
   You can find it in your Wakatime account settings.
4. **Create a personal repository**
   - Your personal repository is a special repository that has the same name as your username. 
   It matches the following pattern : ```<your-username>/<your-username>```. Example : https://github.com/claserre9/claserre9

5. **GitHub Access Token** :
   - You should have a GitHub access token with the `repo` scope. If you don't have one, you can create it in your GitHub 
   account settings.

     
## Usage

To set up and use this GitHub Actions workflow in your repository, follow these steps:

1. **Create Workflow File** :
    - Create a new GitHub Actions workflow file in your personal repository.
   For example, you can create a `.github/workflows/wakatime-stats.yml` file.

2. **Update your README** :
   - Add the following markdown comments to where you want the stats to be generated in your README file.
   ```markdown
   [//]: # (wakatime-stats)
   [//]: # (end-wakatime-stats)
   ```
   The Wakatime will be between these two markdown comments

3. **Configure Workflow** :
    - In the workflow file, specify the following input parameters:
        - `GH_TOKEN`: Your GitHub access token.
        - `WAKATIME_API_KEY`: Your Wakatime API Key.
        - `WAKATIME_USER_ID`: Your Wakatime User ID.

    - Example:
      ```yaml
      name: Wakatime Stats
 
      on:
        workflow_dispatch:
        schedule:
          - cron: '0 0 * * *' #Will at midnight every day. Free to change according to your needs
 
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
      ```

4. **Secrets** :
    - Store your GitHub access token, Wakatime API Key, and Wakatime User ID as secrets in your GitHub repository settings. 
   You can access these secrets as shown in the example workflow above.
   ![secrets-store.png](https://dgn6ny9xamu9c.cloudfront.net/secrets-store.png)

5. **Run Workflow**:
    - The workflow will be triggered when you push changes to your repository's main branch, as per the example configuration. 
   You can customize the trigger events as needed.

## License

This GitHub Actions workflow is provided under the [MIT License](https://opensource.org/license/mit/).
